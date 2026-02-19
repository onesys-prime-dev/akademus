const canvas = document.getElementById('bgCanvas');
const ctx = canvas.getContext('2d');

function resize() {
    canvas.width  = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;
}
resize();
window.addEventListener('resize', resize);

// Particles
const PARTICLE_COUNT = 80;
const particles = [];

class Particle {
    constructor() { this.reset(true); }

    reset(initial = false) {
        this.x  = Math.random() * canvas.width;
        this.y  = initial ? Math.random() * canvas.height : canvas.height + 10;
        this.r  = Math.random() * 3 + 1;
        this.vx = (Math.random() - 0.5) * 0.4;
        this.vy = -(Math.random() * 0.6 + 0.2);
        this.alpha = Math.random() * 0.4 + 0.1;
    }

    update() {
        this.x += this.vx;
        this.y += this.vy;
        if (this.y + this.r < 0) this.reset();
    }

    draw() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(255,255,255,${this.alpha})`;
        ctx.fill();
    }
}

for (let i = 0; i < PARTICLE_COUNT; i++) {
    particles.push(new Particle());
}

// Flowing lines
const lines = [];
const LINE_COUNT = 6;

class FlowLine {
    constructor() { this.reset(true); }

    reset(initial = false) {
        this.x      = Math.random() * canvas.width;
        this.y      = initial ? Math.random() * canvas.height : canvas.height + 20;
        this.length = Math.random() * 120 + 60;
        this.speed  = Math.random() * 0.8 + 0.3;
        this.alpha  = Math.random() * 0.15 + 0.05;
        this.width  = Math.random() * 1.5 + 0.5;
    }

    update() {
        this.y -= this.speed;
        if (this.y + this.length < 0) this.reset();
    }

    draw() {
        ctx.beginPath();
        ctx.moveTo(this.x, this.y + this.length);
        ctx.lineTo(this.x, this.y);
        ctx.strokeStyle = `rgba(255,255,255,${this.alpha})`;
        ctx.lineWidth   = this.width;
        ctx.stroke();
    }
}

for (let i = 0; i < LINE_COUNT; i++) {
    lines.push(new FlowLine());
}

function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    lines.forEach(l => { l.update(); l.draw(); });
    particles.forEach(p => { p.update(); p.draw(); });

    requestAnimationFrame(animate);
}
animate();