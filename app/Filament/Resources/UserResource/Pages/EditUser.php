<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Adjust data before it is saved to the database
     * 
     * @param  array $data
     * @return array
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }

    /**
     * Perform actions after the user record is updated.
     * 
     * This retrieves the selected role from the form data and assigns it
     * to the user. It will remove any previous roles and apply the new one.
     *
     * @return void
     */
    protected function afterSave(): void
    {
        $user = $this->record;

        $formData = $this->form->getState();

        $user->syncRoles([$formData['role']]);
    }

    /**
     * Mutate the form data before it is populated with the current record's data.
     * 
     * This method is called when editing an existing user. It ensures that the role 
     * field is pre-populated with the user's current role if available, or defaults 
     * to 'user' if no role is assigned.
     * 
     * @param  array $data The current form data to be mutated before filling.
     * @return array The mutated form data with the role field set accordingly.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Check if the user is being edited and set the default role accordingly
        if (isset($this->record)) {
            // If editing a user, set the role to the current user's role
            $data['role'] = $this->record->roles->first()->name ?? 'user';
        }

        return $data;
    }
}
