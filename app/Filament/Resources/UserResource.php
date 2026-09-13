<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Kelola User';

    protected static ?string $pluralModelLabel = 'Kelola User';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun & Hak Akses')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->placeholder('Contoh: Siti Aminah / Admin Loket')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('username')
                        ->label('Username Login')
                        ->placeholder('Contoh: bendahara2')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100),

                    Forms\Components\TextInput::make('email')
                        ->label('Alamat Email')
                        ->email()
                        ->placeholder('Contoh: bendahara@sekolah.sch.id')
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\Select::make('role_id')
                        ->label('Peran / Role Pengguna')
                        ->relationship('role', 'display_name')
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Status Akun')
                        ->options([
                            'aktif' => 'Aktif (Dapat Login)',
                            'nonaktif' => 'Nonaktif (Dibekukan)',
                        ])
                        ->default('aktif')
                        ->required(),

                    Forms\Components\TextInput::make('password')
                        ->label('Kata Sandi (Password)')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255)
                        ->helperText('Kosongkan jika tidak ingin mengubah password saat mengedit data.'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->default('-'),

                Tables\Columns\BadgeColumn::make('role.name')
                    ->label('Role')
                    ->colors([
                        'primary' => 'bendahara',
                        'info' => 'siswa',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bendahara' => 'Bendahara / Admin',
                        'siswa' => 'Siswa',
                        default => ucfirst($state),
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'aktif',
                        'danger' => 'nonaktif',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role_id')
                    ->label('Filter Role')
                    ->relationship('role', 'display_name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, User $record) {
                        if ($record->id === Auth::id()) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Aksi Ditolak')
                                ->body('Anda tidak dapat menghapus akun Anda sendiri.')
                                ->send();
                            $action->cancel();
                        }
                    }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
