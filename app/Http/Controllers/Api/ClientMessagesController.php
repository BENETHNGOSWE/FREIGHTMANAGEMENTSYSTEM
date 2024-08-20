<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Http\Resources\MessageCollection;

class ClientMessagesController extends Controller
{
    public function index(Request $request, Client $client): MessageCollection
    {
        $this->authorize('view', $client);

        $search = $request->get('search', '');

        $messages = $client
            ->messages()
            ->search($search)
            ->latest()
            ->paginate();

        return new MessageCollection($messages);
    }

    public function store(Request $request, Client $client): MessageResource
    {
        $this->authorize('create', Message::class);

        $validated = $request->validate([
            'sender_id' => ['nullable', 'max:255'],
            'receiver_id' => ['required', 'max:255'],
            'body' => ['required', 'max:255', 'string'],
            'status' => ['required', 'in:0,1,2'],
            'opened' => ['required', 'boolean'],
        ]);

        $message = $client->messages()->create($validated);

        return new MessageResource($message);
    }
}
