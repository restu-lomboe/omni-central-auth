<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Server;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UserProfileController extends Controller
{
    public function update(Request $request, UpdatesUserProfileInformation $updater)
    {
        $updater->update($request->user(), $request->all());

        return back()->with('status', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request, UpdatesUserPasswords $updater)
    {
        $updater->update($request->user(), $request->all());

        return back()->with('status', 'Password updated successfully.');
    }
}
