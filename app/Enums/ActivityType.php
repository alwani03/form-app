<?php

namespace App\Enums;

enum ActivityType: string
{
    case LIST        = 'List';
    case SEARCH      = 'Search';
    case CREATE      = 'Create';
    case READ        = 'Read';
    case UPDATE      = 'Update';
    case DELETE      = 'Delete';
    case LOGIN       = 'Login';
    case LOGOUT      = 'Logout';

    public function generateRemark(string $module, ?string $details = null): string
    {
        $remark = "{$this->value} {$module}";
        
        if ($details) {
            $remark .= ": {$details}";
        }

        return $remark;
    }
}
