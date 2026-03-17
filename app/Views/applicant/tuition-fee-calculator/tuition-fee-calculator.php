<!-- dashboard/support/tuition_fee_calculator.php -->

<div class="calc-page">

    <div class="calc-header card">
        <div class="calc-header-text">
            <h2>Tuition Fee Calculator</h2>
            <p>Select your subjects to estimate your semester fees. Actual charges may vary — contact the Finance Office for official computation.</p>
        </div>
    </div>

    <div class="calc-body">

        <!-- LEFT: Subject Selector -->
        <div class="calc-left">
            <div class="card">
                <div class="calc-section-title">Select Subjects</div>
                <div class="calc-section-sub">Click a subject to add it. Adjust units using the +/− controls.</div>

                <div class="subj-list" id="subject-list">

                    <div class="subj-item" data-code="ENG101" data-fpu="800" data-max="3">
                        <div class="subj-check" id="check-ENG101"></div>
                        <div class="subj-info">
                            <div class="subj-name">English Communication</div>
                            <div class="subj-meta">ENG101 &middot; max 3 units &middot; ₱800/unit</div>
                        </div>
                        <div class="subj-fee" id="fee-ENG101">₱2,400</div>
                        <div class="units-ctrl" id="ctrl-ENG101" style="display:none;" onclick="event.stopPropagation()">
                            <button class="ubtn" onclick="adjustUnits('ENG101', -1)">−</button>
                            <span class="uval" id="units-ENG101">3</span>
                            <button class="ubtn" onclick="adjustUnits('ENG101', 1)">+</button>
                        </div>
                    </div>

                    <div class="subj-item" data-code="MMW101" data-fpu="800" data-max="3">
                        <div class="subj-check" id="check-MMW101"></div>
                        <div class="subj-info">
                            <div class="subj-name">Mathematics in the Modern World</div>
                            <div class="subj-meta">MMW101 &middot; max 3 units &middot; ₱800/unit</div>
                        </div>
                        <div class="subj-fee" id="fee-MMW101">₱2,400</div>
                        <div class="units-ctrl" id="ctrl-MMW101" style="display:none;" onclick="event.stopPropagation()">
                            <button class="ubtn" onclick="adjustUnits('MMW101', -1)">−</button>
                            <span class="uval" id="units-MMW101">3</span>
                            <button class="ubtn" onclick="adjustUnits('MMW101', 1)">+</button>
                        </div>
                    </div>

                    <div class="subj-item" data-code="GEC101" data-fpu="800" data-max="3">
                        <div class="subj-check" id="check-GEC101"></div>
                        <div class="subj-info">
                            <div class="subj-name">Understanding the Self</div>
                            <div class="subj-meta">GEC101 &middot; max 3 units &middot; ₱800/unit</div>
                        </div>
                        <div class="subj-fee" id="fee-GEC101">₱2,400</div>
                        <div class="units-ctrl" id="ctrl-GEC101" style="display:none;" onclick="event.stopPropagation()">
                            <button class="ubtn" onclick="adjustUnits('GEC101', -1)">−</button>
                            <span class="uval" id="units-GEC101">3</span>
                            <button class="ubtn" onclick="adjustUnits('GEC101', 1)">+</button>
                        </div>
                    </div>

                    <div class="subj-item" data-code="SCI101" data-fpu="850" data-max="3">
                        <div class="subj-check" id="check-SCI101"></div>
                        <div class="subj-info">
                            <div class="subj-name">Natural Sciences</div>
                            <div class="subj-meta">SCI101 &middot; max 3 units &middot; ₱850/unit</div>
                        </div>
                        <div class="subj-fee" id="fee-SCI101">₱2,550</div>
                        <div class="units-ctrl" id="ctrl-SCI101" style="display:none;" onclick="event.stopPropagation()">
                            <button class="ubtn" onclick="adjustUnits('SCI101', -1)">−</button>
                            <span class="uval" id="units-SCI101">3</span>
                            <button class="ubtn" onclick="adjustUnits('SCI101', 1)">+</button>
                        </div>
                    </div>

                    <div class="subj-item" data-code="HIS101" data-fpu="800" data-max="3">
                        <div class="subj-check" id="check-HIS101"></div>
                        <div class="subj-info">
                            <div class="subj-name">Philippine History</div>
                            <div class="subj-meta">HIS101 &middot; max 3 units &middot; ₱800/unit</div>
                        </div>
                        <div class="subj-fee" id="fee-HIS101">₱2,400</div>
                        <div class="units-ctrl" id="ctrl-HIS101" style="display:none;" onclick="event.stopPropagation()">
                            <button class="ubtn" onclick="adjustUnits('HIS101', -1)">−</button>
                            <span class="uval" id="units-HIS101">3</span>
                            <button class="ubtn" onclick="adjustUnits('HIS101', 1)">+</button>
                        </div>
                    </div>

                    <div class="subj-item" data-code="PE101" data-fpu="600" data-max="2">
                        <div class="subj-check" id="check-PE101"></div>
                        <div class="subj-info">
                            <div class="subj-name">Physical Education 1</div>
                            <div class="subj-meta">PE101 &middot; max 2 units &middot; ₱600/unit</div>
                        </div>
                        <div class="subj-fee" id="fee-PE101">₱1,200</div>
                        <div class="units-ctrl" id="ctrl-PE101" style="display:none;" onclick="event.stopPropagation()">
                            <button class="ubtn" onclick="adjustUnits('PE101', -1)">−</button>
                            <span class="uval" id="units-PE101">2</span>
                            <button class="ubtn" onclick="adjustUnits('PE101', 1)">+</button>
                        </div>
                    </div>

                    <div class="subj-item" data-code="CS101" data-fpu="900" data-max="3">
                        <div class="subj-check" id="check-CS101"></div>
                        <div class="subj-info">
                            <div class="subj-name">Computer Fundamentals</div>
                            <div class="subj-meta">CS101 &middot; max 3 units &middot; ₱900/unit</div>
                        </div>
                        <div class="subj-fee" id="fee-CS101">₱2,700</div>
                        <div class="units-ctrl" id="ctrl-CS101" style="display:none;" onclick="event.stopPropagation()">
                            <button class="ubtn" onclick="adjustUnits('CS101', -1)">−</button>
                            <span class="uval" id="units-CS101">3</span>
                            <button class="ubtn" onclick="adjustUnits('CS101', 1)">+</button>
                        </div>
                    </div>

                    <div class="subj-item" data-code="CS102" data-fpu="950" data-max="3">
                        <div class="subj-check" id="check-CS102"></div>
                        <div class="subj-info">
                            <div class="subj-name">Introduction to Programming</div>
                            <div class="subj-meta">CS102 &middot; max 3 units &middot; ₱950/unit</div>
                        </div>
                        <div class="subj-fee" id="fee-CS102">₱2,850</div>
                        <div class="units-ctrl" id="ctrl-CS102" style="display:none;" onclick="event.stopPropagation()">
                            <button class="ubtn" onclick="adjustUnits('CS102', -1)">−</button>
                            <span class="uval" id="units-CS102">3</span>
                            <button class="ubtn" onclick="adjustUnits('CS102', 1)">+</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- RIGHT: Summary -->
        <div class="calc-right">
            <div class="calc-summary">

                <div class="cs-eyebrow">Estimated Breakdown</div>

                <div id="cs-empty" class="cs-empty">
                    Select subjects on the left to see your estimate.
                </div>

                <div id="cs-rows"></div>

                <div id="cs-misc" style="display:none;">
                    <div class="cs-divider"></div>
                    <div class="cs-row">
                        <span class="cs-lbl">Miscellaneous Fees</span>
                        <span class="cs-val">₱3,500</span>
                    </div>
                    <div class="cs-row">
                        <span class="cs-lbl">Laboratory Fee</span>
                        <span class="cs-val">₱1,500</span>
                    </div>
                    <div class="cs-row">
                        <span class="cs-lbl">Student Services Fee</span>
                        <span class="cs-val">₱800</span>
                    </div>
                    <div class="cs-row">
                        <span class="cs-lbl">Registration Fee</span>
                        <span class="cs-val">₱500</span>
                    </div>
                </div>

                <div class="cs-total-row">
                    <span class="cs-total-lbl">Estimated Total</span>
                    <span class="cs-total-val" id="cs-grand-total">₱0</span>
                </div>

                <div class="cs-units-row">
                    <span id="cs-units-txt">0 units selected</span>
                </div>

                <div class="cs-note">
                    Estimate only. Scholarship discounts not reflected. Contact the Finance Office for official computation.
                </div>

                <button class="cs-reset-btn" onclick="resetAll()">Reset All</button>

            </div>
        </div>

    </div>

</div>

<script>
    const subjects = {
        ENG101: { name: 'English Communication',           fpu: 800,  max: 3, units: 3 },
        MMW101: { name: 'Mathematics in the Modern World', fpu: 800,  max: 3, units: 3 },
        GEC101: { name: 'Understanding the Self',          fpu: 800,  max: 3, units: 3 },
        SCI101: { name: 'Natural Sciences',                fpu: 850,  max: 3, units: 3 },
        HIS101: { name: 'Philippine History',              fpu: 800,  max: 3, units: 3 },
        PE101:  { name: 'Physical Education 1',            fpu: 600,  max: 2, units: 2 },
        CS101:  { name: 'Computer Fundamentals',           fpu: 900,  max: 3, units: 3 },
        CS102:  { name: 'Introduction to Programming',     fpu: 950,  max: 3, units: 3 },
    };

    const misc = 3500 + 1500 + 800 + 500; // 6300
    const selected = {};

    function toggleSubject(code) {
        const el = document.querySelector(`.subj-item[data-code="${code}"]`);
        if (selected[code]) {
            delete selected[code];
            el.classList.remove('sel');
            document.getElementById('check-' + code).innerHTML = '';
            document.getElementById('ctrl-' + code).style.display = 'none';
        } else {
            selected[code] = subjects[code].units;
            el.classList.add('sel');
            document.getElementById('check-' + code).innerHTML = '✓';
            document.getElementById('ctrl-' + code).style.display = 'flex';
        }
        updateFeeDisplay(code);
        updateSummary();
    }

    function adjustUnits(code, delta) {
        const subj = subjects[code];
        const current = selected[code] ?? subj.units;
        const newVal = Math.max(1, Math.min(subj.max, current + delta));
        selected[code] = newVal;
        document.getElementById('units-' + code).textContent = newVal;
        updateFeeDisplay(code);
        updateSummary();
    }

    function updateFeeDisplay(code) {
        const subj = subjects[code];
        const units = selected[code] ?? subj.units;
        const fee = units * subj.fpu;
        document.getElementById('fee-' + code).textContent = '₱' + fee.toLocaleString();
    }

    function updateSummary() {
        const codes = Object.keys(selected);
        const csRows = document.getElementById('cs-rows');
        const csEmpty = document.getElementById('cs-empty');
        const csMisc = document.getElementById('cs-misc');
        const grandTotal = document.getElementById('cs-grand-total');
        const unitsTxt = document.getElementById('cs-units-txt');

        if (codes.length === 0) {
            csEmpty.style.display = 'block';
            csRows.innerHTML = '';
            csMisc.style.display = 'none';
            grandTotal.textContent = '₱0';
            unitsTxt.textContent = '0 units selected';
            return;
        }

        csEmpty.style.display = 'none';
        csMisc.style.display = 'block';

        let tuition = 0;
        let totalUnits = 0;
        let html = '';

        codes.forEach(code => {
            const subj = subjects[code];
            const units = selected[code];
            const fee = units * subj.fpu;
            tuition += fee;
            totalUnits += units;
            html += `
                <div class="cs-row">
                    <span class="cs-lbl">${subj.name} (${units} units)</span>
                    <span class="cs-val">₱${fee.toLocaleString()}</span>
                </div>
            `;
        });

        // tuition subtotal row
        html += `
            <div class="cs-row cs-subtotal">
                <span class="cs-lbl">Tuition Subtotal (${totalUnits} units)</span>
                <span class="cs-val">₱${tuition.toLocaleString()}</span>
            </div>
        `;

        csRows.innerHTML = html;
        grandTotal.textContent = '₱' + (tuition + misc).toLocaleString();
        unitsTxt.textContent = totalUnits + ' unit' + (totalUnits !== 1 ? 's' : '') + ' selected';
    }

    function resetAll() {
        Object.keys(selected).forEach(code => {
            delete selected[code];
            const el = document.querySelector(`.subj-item[data-code="${code}"]`);
            el.classList.remove('sel');
            document.getElementById('check-' + code).innerHTML = '';
            document.getElementById('ctrl-' + code).style.display = 'none';
            document.getElementById('units-' + code).textContent = subjects[code].units;
            updateFeeDisplay(code);
        });
        updateSummary();
    }

    // attach click handlers
    document.querySelectorAll('.subj-item').forEach(el => {
        el.addEventListener('click', () => toggleSubject(el.dataset.code));
    });
</script>