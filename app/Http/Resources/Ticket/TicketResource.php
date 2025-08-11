<?php

namespace App\Http\Resources\Ticket;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Message\MessageResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Message\SimpleMessageResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'message' => $this->message,
            'attachment' => $this->attachment ? asset('storage/' . $this->attachment) : null,
            'status' => $this->status,
            'user' => UserResource::make($this->whenLoaded('user')) ?? null,
            'messages' => SimpleMessageResource::collection($this->whenLoaded('messages')) ?? null,
        ];
    }
}
