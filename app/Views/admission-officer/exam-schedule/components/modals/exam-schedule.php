<div class="modal" id="exam-schedule-modal" hidden>
    <div class="modal__dialog">

        <div class="modal__header">
            <h3 class="modal__title">Exam Schedule</h3>
            <button class="modal__close" type="button">&times;</button>
        </div>

        <div class="modal__body">
            <form class="form--fields" id="exam-schedule-form">
                <input type="hidden" name="mode" value="create">
                <input type="hidden" name="exam_schedule_id">

                <div class="form--field">
                    <label for="">Exam Date</label>
                    <input type="date" name="exam_date" required>
                </div>
                <div class="form--row">
                    <div class="form--field">
                        <label for="">Start Time</label>
                        <input type="time" name="start_time" required>
                    </div>
                    <div class="form--field">
                        <label for="">End Time</label>
                        <input type="time" name="end_time" required>
                    </div>
                </div>
                <div class="form--field field-full">
                    <label for="">Location</label>
                    <input type="text" name="location" required>
                </div>
                <div class="form--field field-sm">
                    <label for="">Max Slots</label>
                    <input type="number" name="max_slots" min="1" required>
                </div>
            </form>
        </div>

        <div class="modal__footer">
            <button type="submit" form="exam-schedule-form" class="btn-primary">
                Save
            </button>
        </div>

    </div>
</div>