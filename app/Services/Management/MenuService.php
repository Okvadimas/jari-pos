<?php

namespace App\Services\Management;

use Illuminate\Support\Facades\Auth;

// Load Repository
use App\Repositories\Management\MenuRepository;

class MenuService {

    public static function generateMenu() {
        $user = Auth::user();

        if (!$user) {
            return '';
        }

        $roleId = $user->role_id;

        // Get menu headers from repository
        $headers = MenuRepository::getMenuHeaders($roleId);

        $menuHtml = '';

        foreach ($headers as $header) {
            // Get children from repository
            $children = MenuRepository::getMenuChildren($roleId, $header->code);

            if ($children->isNotEmpty()) {
                $menuHtml .= '<li class="nk-menu-heading">
                    <h6 class="overline-title text-primary-alt">' . $header->name . '</h6>
                </li>';

                foreach ($children as $detail) {
                    $menuHtml .= '<li class="nk-menu-item">
                        <a href="' . $detail->url . '" class="nk-menu-link ">
                            <span class="nk-menu-icon">
                                <em class="icon ' . $detail->icon . '"></em>
                            </span>
                            <span class="nk-menu-text">' . $detail->name . '</span>
                        </a>
                    </li>';
                }
            }
        }

        return $menuHtml;
    }
}
