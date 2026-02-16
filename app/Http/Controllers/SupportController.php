<?php

namespace App\Http\Controllers;

use App\Enum\TicketCategory;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function index(): View
    {
        $ticketCategories = TicketCategory::cases();

        // Generate challenge
        $a = rand(1, 15);
        $b = rand(1, 15);
        session(['support_challenge_answer' => $a + $b]);
        $challenge = "What is {$a} + {$b}?";

        return view('support', compact('ticketCategories', 'challenge'));
    }

}
