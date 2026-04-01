<div class="applicants-per-program card">
    <span class="applicants-per-program__header">Applicants Per Program</span>

    <div class="applicants-per-program__body">
        <svg
            class="applicants-per-program__svg"
            viewBox="0 0 1000 350"
            preserveAspectRatio="none"
        >

            <!-- X AXIS LABELS -->
            <g fill="#888" font-size="12">
                <text x="230" y="20" text-anchor="middle">10</text>
                <text x="565" y="20" text-anchor="middle">100</text>
                <text x="980" y="20" text-anchor="end">1000</text>
            </g>

            <!-- PROGRAM LABELS -->
            <g fill="#333" font-size="12">
                <text x="130" y="70"  text-anchor="end">Engineering</text>
                <text x="130" y="130" text-anchor="end">Arts & Sciences</text>
                <text x="130" y="190" text-anchor="end">Business</text>
                <text x="130" y="250" text-anchor="end">Medicine</text>
                <text x="130" y="310" text-anchor="end">Law</text>
            </g>

            <!-- BARS -->
            <g>
                <!-- Engineering -->
                <rect x="150" y="50" width="750" height="40" rx="6" fill="#3b82f6"/>

                <!-- Arts -->
                <rect x="150" y="110" width="580" height="40" rx="6" fill="#8b5cf6"/>

                <!-- Business -->
                <rect x="150" y="170" width="500" height="40" rx="6" fill="#f59e0b"/>

                <!-- Medicine -->
                <rect x="150" y="230" width="420" height="40" rx="6" fill="#ef4444"/>

                <!-- Law -->
                <rect x="150" y="290" width="350" height="40" rx="6" fill="#10b981"/>
            </g>

        </svg>
    </div>

    <!-- LEGENDS -->
    <div class="applicants-per-program__legends">
        <div class="applicants-per-program__legend">
            <div class="legend-box" style="background:#3b82f6"></div>
            <span>Engineering</span>
        </div>
        <div class="applicants-per-program__legend">
            <div class="legend-box" style="background:#8b5cf6"></div>
            <span>Arts & Sciences</span>
        </div>
        <div class="applicants-per-program__legend">
            <div class="legend-box" style="background:#f59e0b"></div>
            <span>Business</span>
        </div>
        <div class="applicants-per-program__legend">
            <div class="legend-box" style="background:#ef4444"></div>
            <span>Medicine</span>
        </div>
        <div class="applicants-per-program__legend">
            <div class="legend-box" style="background:#10b981"></div>
            <span>Law</span>
        </div>
    </div>
</div>