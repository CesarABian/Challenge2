<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PosnetService
{
    public function register(Request $request)
    {
        if (!in_array($request->type, ['Visa', 'AMEX']))
            return new JsonResponse('type error', 422);
        $number = abs((int) $request->number); 
        if (!strlen((string) $number) === 8)
            return new JsonResponse('card number error', 422);
        return new JsonResponse(Card::create($request->all()), 201);
    }

    public function payment(Request $request)
    {
        $surcharge = 0;
        $amount = $request->amount;
        if ($request->installments > 1) {
            $surcharge = ($request->installments - 1) * 3;
            $amount = $amount + ($amount*$surcharge/100);
        }
        $array = $request->all();
        $array['amount'] = $amount;
        $card = Card::where('number', $array['number']);
        if (!$card)
            return new JsonResponse('card not found', 422);
        if ($amount > $card->limit)
            return new JsonResponse('limit exceed', 422);
        $array['name'] = $card->name . ' ' . $card->lastname;
        $array['installment_amount'] = $amount / $request->installments;
        return new JsonResponse(Ticket::create($request->all()), 201);
    }
}
