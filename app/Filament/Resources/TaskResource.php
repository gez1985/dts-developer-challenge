<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use App\Enums\Priority;
use App\Enums\TaskStatus;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                    TextInput::make('title')
                        ->required()
                        ->maxLength(100),
                    Select::make('priority')
                        ->required()
                        ->default('medium')
                        ->options(Priority::getFilamentSelectOptions()),
                    RichEditor::make('description')
                        ->columnSpan(2)
                        ->maxLength(500),
                    Select::make('status')
                        ->required()
                        ->options(TaskStatus::getFilamentSelectOptions()),
                    DateTimePicker::make('due_date')
                        ->minDate(now())
                        ->maxDate(now()->addYears(2))
                ]
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable()
                    ->numeric(),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn(Priority $state): string => match ($state->value) {
                        'low' => 'gray',
                        'medium' => 'warning',
                        'high' => 'danger',
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(TaskStatus $state): string => match ($state->value) {
                        'pending' => 'gray',
                        'in progress' => 'warning',
                        'completed' => 'success',
                        default => 'secondary',
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('due_date')
                    ->dateTime('F j, Y  @ H:i')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
            ])
            ->filters([
                SelectFilter::make('priority')
                    ->options(Priority::getFilamentSelectOptions()),
                SelectFilter::make('status')
                    ->options(TaskStatus::getFilamentSelectOptions()),
                Filter::make('due_dates')
                    ->form([
                        DatePicker::make('due_after'),
                        DatePicker::make('due_before'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['due_after'],
                                fn(Builder $query, $date): Builder => $query->whereDate('due_date', '>=', $date),
                            )
                            ->when(
                                $data['due_before'],
                                fn(Builder $query, $date): Builder => $query->whereDate('due_date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
