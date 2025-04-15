<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->maxLength(191)
                    ->required(fn(string $context): bool => $context === 'create')
                    ->minLength(8)
                    ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/')
                    ->helperText('Must include at least one uppercase letter, one lowercase letter, and one number.'),
                Select::make('role')
                    ->options(
                        Role::all()->pluck('name')->mapWithKeys(function ($name) {
                            return [$name => Str::title($name)]; // e.g., 'admin' => 'Admin'
                        })->toArray()
                    )
                    ->required()
                    ->default(function ($get) {
                        return $get('role.name') ?: 'user'; // Default to 'user' if no role is set
                    })
                    ->reactive(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'warning',
                        'user' => 'success',
                        'super-admin' => 'danger',
                    })
                    ->getStateUsing(function (User $record) {
                        return $record->getRoleNames()->first(); // Fetch the first role name
                    }),
                TextColumn::make('created_at')
                    ->label('member since')
                    ->sortable(),
            ])
            ->filters([])
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
        return [
            //
        ];
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
