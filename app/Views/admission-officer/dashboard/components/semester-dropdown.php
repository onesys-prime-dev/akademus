<select 
    name="<?= $name ?? 'program_id' ?>" 
    id="<?= $id ?? 'program-dropdown' ?>" 
    class="program-dropdown <?= $class ?? '' ?>" 
    data-program
>
    <option value="">Semester</option>

    <?php foreach ($programs as $program): ?>

        <?php
            if (preg_match('/Bachelor of Science in/', $program['program_name'])) {
                $programName = preg_replace('/Bachelor of Science in/', 'BS', $program['program_name']);
            }
        ?>

        <option 
            value="<?= esc($program['program_id']) ?>"
            <?= isset($selected) && $selected == $program['program_id'] ? 'selected' : '' ?>
        >
            <?= esc($programName) ?>
        </option>
    <?php endforeach; ?>
</select>