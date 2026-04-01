<div class="applicants-per-status card">
    <span class="applicants-per-status__header">Applicants Per Status</span>

    <div class="applicants-per-status__body">
        <svg
            class="applicants-per-status__svg"
            viewBox="0 0 1000 280"
            preserveAspectRatio="none"
            role="img"
            aria-label="Applicants per status bar chart"
        >
            <!-- grid lines -->
            <g class="applicants-per-status__grid">
                <line x1="90" y1="20"  x2="980" y2="20"  />
                <line x1="90" y1="68"  x2="980" y2="68"  />
                <line x1="90" y1="116" x2="980" y2="116" />
                <line x1="90" y1="164" x2="980" y2="164" />
                <line x1="90" y1="212" x2="980" y2="212" />
                <line x1="90" y1="260" x2="980" y2="260" />
            </g>

            <!-- y-axis labels -->
            <g class="applicants-per-status__y-axis">
                <text x="80" y="24">900</text>
                <text x="80" y="72">800</text>
                <text x="80" y="120">600</text>
                <text x="80" y="168">400</text>
                <text x="80" y="216">200</text>
                <text x="80" y="264">0</text>
            </g>

            <!-- bars -->
            <g class="applicants-per-status__bars">
                <!-- Submitted -->
                <rect x="150" y="45" width="140" height="215" rx="8" />
                <!-- Pending -->
                <rect x="365" y="112" width="140" height="148" rx="8" />
                <!-- Approved -->
                <rect x="580" y="82" width="140" height="178" rx="8" />
                <!-- Rejected -->
                <rect x="795" y="165" width="140" height="95" rx="8" />
            </g>
        </svg>
    </div>

    <div class="applicants-per-status__legends">
        <div class="applicants-per-status__legend">
            <div class="applicants-per-status__legend--box applicants-per-status__legend--box-submitted"></div>
            <div class="applicants-per-status__legend--title">Submitted</div>
        </div>

        <div class="applicants-per-status__legend">
            <div class="applicants-per-status__legend--box applicants-per-status__legend--box-pending"></div>
            <div class="applicants-per-status__legend--title">Pending</div>
        </div>

        <div class="applicants-per-status__legend">
            <div class="applicants-per-status__legend--box applicants-per-status__legend--box-approved"></div>
            <div class="applicants-per-status__legend--title">Approved</div>
        </div>

        <div class="applicants-per-status__legend">
            <div class="applicants-per-status__legend--box applicants-per-status__legend--box-rejected"></div>
            <div class="applicants-per-status__legend--title">Rejected</div>
        </div>
    </div>
</div>