<div class="modal" id="schedule-details-modal" hidden>
    <div class="modal__dialog modal__dialog--lg">

        <div class="modal__header">
            <h3 class="modal__title">Schedule Details</h3>
            <button class="modal__close" type="button">&times;</button>
        </div>

        <div class="modal__body">
            <div class="schedule-details__summary">

                <div class="summary-card">
                    <span class="summary-label">Date</span>
                    <span class="summary-value" data-detail="date"></span>
                </div>

                <div class="summary-card">
                    <span class="summary-label">Time</span>
                    <span class="summary-value" data-detail="time"></span>
                </div>

                <div class="summary-card summary-card--wide">
                    <span class="summary-label">Location</span>
                    <span class="summary-value" data-detail="location"></span>
                </div>

                <div class="summary-card">
                    <span class="summary-label">Slots</span>
                    <span class="summary-value" data-detail="slots"></span>
                </div>

                <div class="summary-card">
                    <span class="summary-label">Status</span>
                    <span class="summary-value summary-status" data-detail="status"></span>
                </div>

                <div class="summary-card">
                    <span class="summary-label">Created</span>
                    <span class="summary-value" data-detail="created_at"></span>
                </div>

            </div>

            <div class="schedule-details__applicants">
                <table>
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Booked</th>
                            <th>Check-in</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody data-applicants-body></tbody>
                </table>
            </div>
        </div>

    </div>
</div>