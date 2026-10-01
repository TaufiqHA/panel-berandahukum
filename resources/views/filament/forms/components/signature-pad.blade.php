@once
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
@endonce

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            pad: null,
            mode: 'draw',
            init() {
                this.$nextTick(() => this.setupPad());
            },
            setupPad() {
                if (typeof SignaturePad === 'undefined') {
                    setTimeout(() => this.setupPad(), 150);
                    return;
                }
                const canvas = this.$refs.canvas;
                if (! canvas) {
                    return;
                }
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                this.pad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: 'rgb(0, 0, 0)',
                });
                if (this.state) {
                    this.pad.fromDataURL(this.state);
                }
            },
            clear() {
                if (this.pad) {
                    this.pad.clear();
                }
                this.state = null;
            },
            save() {
                if (this.pad && ! this.pad.isEmpty()) {
                    this.state = this.pad.toDataURL('image/png');
                }
            },
            upload(event) {
                const file = event.target.files[0];
                if (! file) {
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.state = e.target.result;
                    if (this.pad) {
                        this.pad.fromDataURL(e.target.result);
                    }
                };
                reader.readAsDataURL(file);
            },
        }"
        style="display:flex;flex-direction:column;gap:12px;"
    >
        <div style="display:flex;gap:8px;">
            <button
                type="button"
                x-on:click="mode = 'draw'"
                x-bind:style="mode === 'draw'
                    ? 'background:#f59e0b;color:#fff;border:1px solid #f59e0b;'
                    : 'background:#fff;color:#374151;border:1px solid #d1d5db;'"
                style="padding:6px 14px;border-radius:6px;font-size:13px;cursor:pointer;"
            >
                Gambar Langsung
            </button>
            <button
                type="button"
                x-on:click="mode = 'upload'"
                x-bind:style="mode === 'upload'
                    ? 'background:#f59e0b;color:#fff;border:1px solid #f59e0b;'
                    : 'background:#fff;color:#374151;border:1px solid #d1d5db;'"
                style="padding:6px 14px;border-radius:6px;font-size:13px;cursor:pointer;"
            >
                Upload PNG / Foto
            </button>
        </div>

        <div x-show="mode === 'draw'">
            <canvas
                x-ref="canvas"
                x-on:mouseup="save()"
                x-on:touchend="save()"
                style="width:100%;height:250px;border:2px dashed #d1d5db;border-radius:8px;background:#f9fafb;cursor:crosshair;touch-action:none;"
            ></canvas>
            <button
                type="button"
                x-on:click="clear()"
                style="margin-top:8px;padding:6px 14px;border-radius:6px;font-size:13px;cursor:pointer;background:#fff;color:#b45309;border:1px solid #f59e0b;"
            >
                Bersihkan Canvas
            </button>
        </div>

        <div x-show="mode === 'upload'" x-cloak>
            <input
                type="file"
                accept="image/png,image/jpeg"
                x-on:change="upload($event)"
                style="display:block;width:100%;font-size:13px;"
            >
        </div>

        <template x-if="state">
            <div>
                <p style="font-size:12px;font-weight:600;color:#6b7280;margin-bottom:4px;">Pratinjau:</p>
                <img
                    x-bind:src="state"
                    alt="Tanda Tangan"
                    style="max-height:150px;background:#fff;border:1px solid #d1d5db;padding:4px;border-radius:4px;"
                >
            </div>
        </template>
    </div>
</x-dynamic-component>
