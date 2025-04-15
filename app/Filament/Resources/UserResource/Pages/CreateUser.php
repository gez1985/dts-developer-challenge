<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Hash password before creating user
     *
     * @param  array $data
     * @return array
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        return $data;
    }

    /**
     * Assign the selected role to the newly created user.
     *
     * This method runs after the user record has been created.
     * It retrieves the role name from the form data and assigns
     * the corresponding role to the user using Spatie's permission package.
     *
     * @return void
     */
    protected function afterCreate(): void
    {
        $user = $this->record;

        // Get the form data
        $formData = $this->form->getState();

        $user->assignRole($formData['role']);
    }
}
