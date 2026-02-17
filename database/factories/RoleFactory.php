<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
	protected $model = Role::class;

	public function definition()
	{
		// Default to a regular user role; tests create specific roles via Role::firstOrCreate
		return [
			'name' => 'user',
			'display_name' => 'Utilisateur',
		];
	}

	/**
	 * State for admin role
	 */
	public function admin()
	{
		return $this->state(function () {
			return ['name' => 'admin', 'display_name' => 'Administrateur'];
		});
	}

	/**
	 * State for editor role
	 */
	public function editor()
	{
		return $this->state(function () {
			return ['name' => 'editor', 'display_name' => 'Editeur'];
		});
	}
}

