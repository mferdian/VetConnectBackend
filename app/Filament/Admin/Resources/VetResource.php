<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VetResource\Pages;
use App\Filament\Admin\Resources\VetResource\RelationManagers;
use App\Models\Vet;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VetResource extends Resource
{
    protected static ?string $model = Vet::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Veterinarians';
    protected static ?string $modelLabel = 'Veterinarian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Section::make('Informasi Pribadi')
                    ->description('Detail personal dokter hewan.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()->required()
                            ->placeholder('dokter@mail.com'),
                        TextInput::make('no_telp')->label('No. Telepon')
                            ->tel()->required(),

                        Radio::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                1 => 'Laki-laki',
                                0 => 'Perempuan',
                            ])
                            ->inline()
                            ->required(),

                        DatePicker::make('tgl_lahir')->label('Tanggal Lahir')->native(false)->required(),
                        Textarea::make('alamat')->label('Alamat')->rows(3)->placeholder('Jl. Kenangan No. 12')->required(),
                    ])->columns(2),

               Section::make('Informasi Profesional')
                    ->description('STR, SIP, dan spesialisasi dokter.')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        TextInput::make('STR')->label('Nomor STR')->required()->maxLength(50),
                        TextInput::make('SIP')->label('Nomor SIP')->required()->maxLength(50),
                        Select::make('spesialisasis')
                            ->relationship('spesialisasis', 'nama_hewan')
                            ->multiple()
                            ->label('Spesialisasi Hewan')
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('nama_hewan')
                                    ->required()
                                    ->maxLength(255)
                            ])
                            ->required(),
                        Textarea::make('deskripsi')->label('Deskripsi')->rows(4)->required()->placeholder('Tuliskan pengalaman atau spesialisasi dokter...'),
                        TextInput::make('harga')->prefix('Rp')->label('Harga Konsultasi')->required()->placeholder('50000'),
                    ]),

               Section::make('Foto Profil')
                    ->description('Unggah foto dokter.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('foto')->label('Foto Profil')->image()->imageEditor()->maxSize(2048)->required(),
                    ]),

               Section::make('Jadwal Praktik')
                    ->description('Tanggal dan jam praktik dokter.')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Repeater::make('vetDates')
                            ->label('Tanggal Praktik')
                            ->relationship('vetDates')
                            ->schema([
                                DatePicker::make('tanggal')->label('Tanggal')->native(false)->required(),
                                Repeater::make('vetTimes')
                                ->label('Jam Praktik')
                                ->relationship('vetTimes')
                                ->schema([
                                    TimePicker::make('jam_mulai')->label('Jam Mulai')->required(),
                                    TimePicker::make('jam_selesai')->label('Jam Selesai')->required(),
                                ])
                                ->addActionLabel('Tambah Jam')
                                ->columns(2),
                            ])
                            ->addActionLabel('Tambah Tanggal')
                            ->columns(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')->label('Foto')->circular(),
                TextColumn::make('nama')->label('Nama Lengkap')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('STR')->label('STR')->searchable()->sortable(),
                TextColumn::make('SIP')->label('SIP')->searchable()->sortable(),
                IconColumn::make('jenis_kelamin')
                    ->label('Gender')
                    ->boolean()
                    ->trueIcon('heroicon-o-user')
                    ->falseIcon('heroicon-o-user')
                    ->trueColor('primary')
                    ->falseColor('danger')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn (bool $state): string => $state ? 'Laki-laki' : 'Perempuan'),

                TextColumn::make('spesialisasis.nama_hewan')
                    ->label('Spesialisasi')
                    ->badge()
                    ->separator(', ')
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\Filter::make('jenis_kelamin')
                    ->label('Gender')
                    ->form([
                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                '1' => 'Laki-Laki',
                                '0' => 'Perempuan',
                            ])
                            ->placeholder('Semua')
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['jenis_kelamin'] !== null,
                            fn ($query) => $query->where('jenis_kelamin', $data['jenis_kelamin'])
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVets::route('/'),
            'create' => Pages\CreateVet::route('/create'),
            'edit' => Pages\EditVet::route('/{record}/edit'),
        ];
    }
}
