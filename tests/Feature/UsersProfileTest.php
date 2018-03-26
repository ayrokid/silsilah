<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Storage;
use Tests\TestCase;

class UsersProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_other_users_profile()
    {
        $user = factory(User::class)->create();
        $this->visit(route('users.show', $user->id));
        $this->see($user->name);
    }

    /** @test */
    public function user_can_edit_profile()
    {
        $user = $this->loginAsUser();
        $this->visit(route('users.edit', $user->id));
        $this->seePageIs(route('users.edit', $user->id));

        $this->submitForm(trans('app.update'), [
            'nickname'  => 'Nama Panggilan',
            'name'      => 'Nama User',
            'gender_id' => 1,
            'dob'       => '1959-06-09',
            'dod'       => '2003-10-17',
            'yod'       => '',
            'address'   => 'Jln. Angkasa, No. 70',
            'city'      => 'Nama Kota',
            'phone'     => '081234567890',
            'email'     => '',
            'password'  => '',
        ]);

        $this->seeInDatabase('users', [
            'nickname'  => 'Nama Panggilan',
            'name'      => 'Nama User',
            'gender_id' => 1,
            'dob'       => '1959-06-09',
            'dod'       => '2003-10-17',
            'yod'       => '2003',
            'address'   => 'Jln. Angkasa, No. 70',
            'city'      => 'Nama Kota',
            'phone'     => '081234567890',
            'email'     => null,
            'password'  => null,
        ]);
    }

    /** @test */
    public function user_can_upload_their_own_photo()
    {
        Storage::fake('avatars');

        $user = $this->loginAsUser();
        $this->visit(route('users.edit', $user->id));
        $this->assertNull($user->photo_path);

        $this->attach(public_path('images/icon_user_1.png'), 'photo');
        $this->press(trans('user.update_photo'));

        $this->seePageIs(route('users.edit', $user));

        $user = $user->fresh();

        $this->assertNotNull($user->photo_path);
        Storage::disk('avatars')->assertExists($user->photo_path);
    }

    /** @test */
    public function guest_can_search_users_profile()
    {
        $this->visit(route('users.search'));
        $this->seePageIs(route('users.search'));
    }
}
