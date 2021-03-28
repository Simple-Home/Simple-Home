<?php

namespace App\Forms;

use App\Models\User;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\Field;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileInformationForm extends Form
{
    public function buildForm()
    {
        $user = Auth::user();
        $this
            ->add('name', Field::TEXT, [
                'rules' => 'required|max:255',
                'label' => "Username"
            ])
            ->add('email', Field::EMAIL, [
                'attr' => ['disabled' => true],
                'label' => "Email"
            ])
            ->add('saveProfile', Field::BUTTON_SUBMIT, [
                'label' => "Save"
            ]);
    }
}
