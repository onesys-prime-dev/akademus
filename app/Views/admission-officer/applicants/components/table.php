<div class="applicants-table card">
    <div class="applicants-table__loading" id="table-loading" hidden>
        Loading...
    </div>

    <table class="applicants-table__table">
        <thead class="applicants-table__head">
            <tr class="applicants-table__row applicants-table__row--head">
                <th class="applicants-table__cell applicants-table__cell--checkbox"></th>
                <th class="applicants-table__cell">Applicant Name</th>
                <th class="applicants-table__cell">Email</th>
                <th class="applicants-table__cell">Applicant ID</th>
                <th class="applicants-table__cell">Applicant Type</th>
                <th class="applicants-table__cell">Program</th>
                <th class="applicants-table__cell">Status</th>
                <th class="applicants-table__cell">Date Added</th>
                <th class="applicants-table__cell applicants-table__cell--actions-header">Actions</th>
            </tr>
        </thead>

        <tbody class="applicants-table__body" id="applicants-table-body">
        </tbody>
    </table>

    <?= $this->include('admission-officer/components/pagination') ?>
</div>