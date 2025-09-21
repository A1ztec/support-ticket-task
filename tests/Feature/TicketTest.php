<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;


class TicketTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {

        Parent::setUp();
        $this->artisan('config:clear');
    }

    #[Test]
    public function simulate_login_and_get_all_tickets_for_user_test()
    {
        $user = User::factory()->has(Ticket::factory()->count(3))->create();
        $response = $this->actingAs($user)->getJson('/api/v1/tickets');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'code',
                'data' => [
                    '*' => [
                        'id',
                        'subject',
                        'message',
                        'attachment',
                        'status',
                        'user' => [
                            'id',
                            'name',
                            'email',
                        ],
                        'messages' => [
                            '*' => [
                                'id',
                                'message',
                                'user' => [
                                    'id',
                                    'name',
                                    'email',
                                ]

                            ]
                        ]
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function simulate_guest_user_trying_to_get_tickets_test()
    {
        // $this->expectException(AuthenticationException::class);

        $response = $this->getJson('/api/v1/tickets');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'message',
                'code',
            ]);
    }

    #[Test]
    public function simulate_login_and_get_no_tickets_for_user_test()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/v1/tickets');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'code',
                'data'
            ])
            ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function simulate_login_and_get_single_ticket_for_user_test()
    {
        $user = User::factory()->has(Ticket::factory()->count(1))->create();


        $ticket = $user->tickets()->first();

        $response = $this->actingAs($user)->getJson('/api/v1/tickets/' . $ticket->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'code',
                'data' => [
                    'id',
                    'subject',
                    'message',
                    'attachment',
                    'status',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'messages' => [
                        '*' => [
                            'id',
                            'message',
                            'user' => [
                                'id',
                                'name',
                                'email',
                            ]

                        ]
                    ]
                ]
            ]);
    }

    #[Test]
    public function simulate_login_and_get_single_ticket_not_belonging_to_user_test()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->has(Ticket::factory()->count(1))->create();
        $ticket = $user2->tickets()->first();
        $response = $this->actingAs($user)->getJson('/api/v1/tickets/' . $ticket->id);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'status',
                'message',
                'code',
            ])->assertJson([
                'status' => 'error',
                'message' => __('You are not authorized to perform this action.'),
                'code' => 401,
            ]);
    }

    #[Test]
    public function simulate_user_trying_to_create_ticket_test()
    {
        $user = User::factory()->create();

        $response = $this->withHeaders(['Accept-Language' => 'ar'])->actingAs($user)->postJson('/api/v1/tickets', [
            'subject' => 'Test Subject',
            'message' => 'Test Message',
        ]);


        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'code',
                'data' => [
                    'id',
                    'subject',
                    'message',
                    'attachment',
                    'status',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'messages'
                ]
            ])->assertJson([
                'status' => 'success',
                'message' => __('Ticket created successfully.'),
                'code' => 201,
            ]);
    }

    public function test_simulate_user_trying_to_reply_to_ticket_test()
    {
        $user = User::factory()->has(Ticket::factory()->count(1))->create();
        $ticket = $user->tickets()->first();

        $response = $this->withHeaders(['Accept-Language' => 'ar'])->actingAs($user)->postJson('/api/v1/tickets/reply', [
            'message' => __('This is reply message'),
            'ticket_id' => $ticket->id
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'code',
                'data' => [
                    'id',
                    'subject',
                    'message',
                    'attachment',
                    'status',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'messages' => [
                        '*' => [
                            'id',
                            'message',
                            'user' => [
                                'id',
                                'name',
                                'email',
                            ]

                        ]
                    ]
                ]
            ])->assertJson([
                'status' => 'success',
                'message' => __('Reply sent successfully.'),
            ]);
    }

    #[Test]
    public function simulate_user_trying_to_reply_to_ticket_not_belonging_to_them_test()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->has(Ticket::factory()->count(1))->create();
        $ticket = $user2->tickets()->first();

        $response = $this->withHeaders(['Accept-Language' => 'ar'])->actingAs($user)->postJson('/api/v1/tickets/reply', [
            'message' => __('This is reply message'),
            'ticket_id' => $ticket->id
        ])->assertStatus(401)
            ->assertJsonStructure([
                'status',
                'message',
                'code',
            ])->assertJson([
                'status' => 'error',
                'message' => __('You are not authorized to perform this action.'),
                'code' => 401,
            ]);
    }
}
