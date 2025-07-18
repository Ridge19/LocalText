<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\SupportAttachment;
use App\Constants\Status;

class TicketController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layout       = 'frontend';
        $this->redirectLink = 'ticket.view';
        $this->userType     = 'user';
        $this->column       = 'user_id';
        $this->user         = Auth::user();
        if ($this->user) {
            $this->layout = 'master';
        }
    }

    /**
     * Display a listing of the user's tickets.
     */
    public function index()
    {
        $supports  = SupportTicket::where('user_id', Auth::id())->paginate(10);
        $pageTitle = 'My Support Tickets';
        return view('templates.basic.user.support.index', compact('pageTitle', 'supports'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function open()
    {
        $pageTitle = 'Open New Support Ticket';
        return view('templates.basic.user.support.create', compact('pageTitle'));
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject'       => 'required|string|max:255',
            'priority'      => 'required|in:1,2,3',
            'message'       => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ], [
            'attachments.*.mimes' => 'Allowed types: jpg, jpeg, png, pdf, doc, docx',
            'attachments.*.max'   => 'Each attachment must be under 2MB',
        ]);

        $ticket = SupportTicket::create([
            'user_id'    => Auth::id(),
            'ticket'     => strtoupper(Str::random(10)),
            'subject'    => $request->subject,
            'priority'   => $request->priority,
            'status'     => Status::TICKET_OPEN,
            'last_reply' => now(),
        ]);

        $message = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => Auth::id(),
            'message'           => $request->message,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store("tickets/{$ticket->id}", 'public');
                SupportAttachment::create([
                    'support_message_id' => $message->id,
                    'attachment'         => $path,
                ]);
            }
        }

        return redirect()->route('user.ticket.index')
                         ->with('success', 'Ticket created successfully.');
    }
}
