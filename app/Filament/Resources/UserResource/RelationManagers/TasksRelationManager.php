<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\Priority;
use App\Enums\TaskStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title'),
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
