<x-app-layout>
    <x-slot:title>Pricing</x-slot:title>
    <section id="packages" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Title -->
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-display font-bold text-primary-900 mb-6">Our Packages</h2>
                <p class="text-xl text-primary-600 max-w-3xl mx-auto">
                    Choose the plan that fits your needs. Simple pricing, no hidden fees.
                </p>
            </div>

            <!-- Pricing Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($packages as $package)
                    <x-pricing-card :package="$package" />
                @endforeach
            </div>
        </div>
    </section>

</x-app-layout>
