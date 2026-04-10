<div class="modal" id="schedule-details-modal" hidden>
    <div class="modal__dialog modal__dialog--lg">

        <div class="modal__header">
            <h3 class="modal__title">Schedule Details</h3>
            <button class="modal__close" type="button">&times;</button>
        </div>

        <div class="modal__body">
            <div class="schedule-details__summary">

                <!-- ROW 1 (PRIMARY CARDS) -->
                <div class="summary-card">
                    <span class="summary-label">Date</span>
                    <span class="summary-value" data-detail="date"></span>
                </div>

                <div class="summary-card">
                    <span class="summary-label">Time</span>
                    <span class="summary-value" data-detail="time"></span>
                </div>

                <div class="summary-card">
                    <span class="summary-label">Status</span>
                    <span class="summary-value summary-status" data-detail="status"></span>
                </div>

                <div class="summary-card">
                    <span class="summary-label">Slots</span>
                    <span class="summary-value" data-detail="slots"></span>
                </div>

                <!-- ROW 2 (SECONDARY METADATA) -->
                <div class="summary-card summary-card--location">
                    <span class="summary-label">Location</span>
                </div>

                <div class="summary-card">
                    <span class="summary-value" data-detail="location"></span>
                </div>

                <div class="summary-card summary-card--created">
                    <span class="summary-label">Created</span>
                </div>

                <div class="summary-card">
                    <span class="summary-value" data-detail="created_at"></span>
                </div>

            </div>

            <h4 class="schedule-details__applicants--title">Booked Applicants</h4>

            <div class="exam-schedule-table__card card">
                <table class="exam-schedule-table__table">
                    <thead class="exam-schedule-table__head">
                        <tr class="exam-schedule-table__row--head">
                            <th class="exam-schedule-table__cell">Applicant</th>
                            <th class="exam-schedule-table__cell">Contact</th>
                            <th class="exam-schedule-table__cell">Status</th>
                            <th class="exam-schedule-table__cell">Booking Date</th>
                            <th class="exam-schedule-table__cell">Check-in</th>
                            <th class="exam-schedule-table__cell exam-schedule-table__cell--actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="exam-schedule-table__body" data-applicants-body></tbody>
                </table>
            </div>
        </div>

    </div>
</div>