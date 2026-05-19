<div class="clock-widget mt-8 px-4">
    <div class="relative w-32 h-32 mx-auto">
        <!-- Clock SVG -->
        <svg viewBox="0 0 160 160" class="w-full h-full transform -rotate-90">
            <!-- Outer circle with gradient -->
            <defs>
                <linearGradient id="clockGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#D4965A;stop-opacity:0.3" />
                    <stop offset="100%" style="stop-color:#E9C46A;stop-opacity:0.5" />
                </linearGradient>
            </defs>

            <!-- Background circle -->
            <circle cx="80" cy="80" r="70" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2" />

            <!-- Hour markers -->
            @for($i = 0; $i < 12; $i++)
                @php
                    $angle = $i * 30;
                    $x1 = 80 + 60 * cos(deg2rad($angle));
                    $y1 = 80 + 60 * sin(deg2rad($angle));
                    $x2 = 80 + 65 * cos(deg2rad($angle));
                    $y2 = 80 + 65 * sin(deg2rad($angle));
                @endphp
                <line x1="{{ $x1 }}" y1="{{ $y1 }}" x2="{{ $x2 }}" y2="{{ $y2 }}" stroke="rgba(255,255,255,0.5)"
                    stroke-width="2" stroke-linecap="round" />
            @endfor

            <!-- Hour hand -->
            <line id="hourHand" x1="80" y1="80" x2="80" y2="50" stroke="#E9C46A" stroke-width="4" stroke-linecap="round"
                style="transform-origin: 80px 80px;" />

            <!-- Minute hand -->
            <line id="minuteHand" x1="80" y1="80" x2="80" y2="35" stroke="#F4A261" stroke-width="3"
                stroke-linecap="round" style="transform-origin: 80px 80px;" />

            <!-- Second hand -->
            <line id="secondHand" x1="80" y1="80" x2="80" y2="30" stroke="#EF476F" stroke-width="2"
                stroke-linecap="round" style="transform-origin: 80px 80px;" />

            <!-- Center dot -->
            <circle cx="80" cy="80" r="4" fill="#D4965A" />
        </svg>

        <!-- Digital time display -->
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center mt-16">
                <div id="digitalTime" class="text-xs font-medium text-bakery-cream/80"></div>
            </div>
        </div>
    </div>

    <p class="text-center text-xs text-bakery-cream/60 mt-2">{{ now()->format('d M Y') }}</p>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const hours = now.getHours();
        const minutes = now.getMinutes();
        const seconds = now.getSeconds();

        // Calculate angles (in degrees)
        const secondAngle = (seconds * 6); // 360/60
        const minuteAngle = (minutes * 6) + (seconds * 0.1); // 360/60 + smooth transition
        const hourAngle = ((hours % 12) * 30) + (minutes * 0.5); // 360/12 + smooth transition

        // Update hand positions
        const hourHand = document.getElementById('hourHand');
        const minuteHand = document.getElementById('minuteHand');
        const secondHand = document.getElementById('secondHand');

        if (hourHand) hourHand.style.transform = `rotate(${hourAngle}deg)`;
        if (minuteHand) minuteHand.style.transform = `rotate(${minuteAngle}deg)`;
        if (secondHand) secondHand.style.transform = `rotate(${secondAngle}deg)`;

        // Update digital time
        const digitalTime = document.getElementById('digitalTime');
        if (digitalTime) {
            const timeString = now.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });
            digitalTime.textContent = timeString;
        }
    }

    // Update clock immediately and then every second
    updateClock();
    setInterval(updateClock, 1000);
</script>