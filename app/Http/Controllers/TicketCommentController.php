<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TicketCommentController extends Controller
{
    public function store(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('comment', $ticket);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Komentar ditambahkan.');
    }
}
