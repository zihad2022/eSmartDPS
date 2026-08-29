 @php
     $client = \App\Models\Client::find(owner_client_id());
     $activeClientPackage = $client?->activeClientPackage;
     $activePackage = $activeClientPackage?->package;
 @endphp

 <div class="flex items-center">
     <button id="sidebarToggle" class="text-primary-500 hover:text-primary-700 focus:outline-none mr-4 md:hidden">
         <i class="fas fa-bars text-xl"></i>
     </button>
     <h1 class="text-xl font-semibold text-primary-900">Dashboard</h1>

     {{-- @if ($activePackage)
                    @php
                        $endsAt = $activeClientPackage->ends_at;
                        $remaining =
                            $endsAt && $endsAt->isFuture()
                                ? $endsAt->diffForHumans(now(), [
                                    'parts' => 2,
                                    'short' => true,
                                    'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                                ])
                                : 'Expired';
                    @endphp

                    @if ($activePackage->has_trial && $activePackage->trial_days > 0)
                        <span class="text-green-600 font-semibold pl-2">
                            Free Trial ({{ $remaining }})
                        </span>
                    @else
                        <span class="text-blue-600 font-semibold pl-2">
                            Paid Subscription ({{ $remaining }})
                        </span>
                    @endif
                @else
                    <span class="text-gray-500 pl-2">No Active Subscription</span>
                @endif --}}
     @if ($activePackage && $activeClientPackage?->ends_at)
         <div x-data="countdown('{{ $activeClientPackage->ends_at }}')" x-init="start()" class="font-semibold pl-2">
             @if ($activePackage->has_trial)
                 <span class="text-green-600">Free Trial (<span x-text="remaining"></span>)</span>
             @else
                 <span class="text-blue-600">Paid Subscription (<span x-text="remaining"></span>)</span>
             @endif
         </div>
     @else
         <span class="text-gray-500 pl-2">No Active Subscription</span>
     @endif
 </div>
