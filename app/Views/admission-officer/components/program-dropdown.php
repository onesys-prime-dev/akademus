<select 
    name="<?= $name ?? 'program_id' ?>" 
    id="<?= $id ?? 'program-dropdown' ?>" 
    class="program-dropdown <?= $class ?? '' ?>" 
    data-program
>
    <option value="">All Programs</option>

    <?php foreach ($programs as $program): ?>

        <option 
            value="<?= esc($program['program_id']) ?>"
            <?= isset($selected) && $selected == $program['program_id'] ? 'selected' : '' ?>
        >
            <?= esc($program['program_code'] ?: $program['program_name']) ?>
        </option>
    <?php endforeach; ?>
</select>