@component('mail::message')
# Weekly Trip Summary ({{ $trip->name }})

Hello Travellers,

Here is your weekly summary for your trip **{{ $trip->name }}**.

---

## 🧾 Expenses Added This Week
@if(count($expenses) == 0)
_No expenses added this week._
@else
@foreach($expenses as $exp)
- **{{ $exp['payer']['name'] ?? 'Unknown' }}** paid **₹{{ $exp['amount'] ?? 0 }}** for **{{ $exp['description'] ?? 'No description' }}**
@endforeach
@endif

---

## 🗒️ Itinerary Updates This Week
@if(count($itineraries) == 0)
_No itinerary updates this week._
@else
@foreach($itineraries as $it)
- **Day {{ $it->day_number }}** – {{ $it->title }}  
  ({{ $it->start_time }} to {{ $it->end_time }})
@endforeach
@endif

---

## 💰 Current Balances
@if(count($balances) == 0)
_All settled. No pending balances._
@else
@foreach($balances as $balance)
- **User {{ $balance['payee_id'] }} → owes → User {{ $balance['payer_id'] }}:** ₹{{ $balance['amount'] }}
@endforeach
@endif

---

Thanks,<br>
{{ config('app.name') }}
@endcomponent
