<div class="emcaptcha-container" style="display: inline-flex; align-items: center; gap: 8px;">
    <img
        src="{{ route('emcaptcha.show') }}?_={{ time() }}"
        alt="Captcha"
        id="emcaptcha-img"
        style="border: 1px solid #ccc; border-radius: 4px;"
    >
    <button
        type="button"
        onclick="refreshEMCaptcha()"
        style="background: none; border: 1px solid #ccc; border-radius: 4px; padding: 6px 10px; cursor: pointer; font-size: 18px;"
        title="Generar nuevo captcha"
    >&#x1F504;</button>
    <input
        type="number"
        name="{{ $name ?? 'captcha' }}"
        id="emcaptcha-input"
        placeholder="{{ $placeholder ?? 'Resultado' }}"
        required
        style="border: 1px solid #ccc; border-radius: 4px; padding: 6px 10px; width: 100px; font-size: 16px;"
    >
</div>

<script>
function refreshEMCaptcha() {
    fetch('{{ route("emcaptcha.refresh") }}')
        .then(function(response) { return response.json(); })
        .then(function(data) {
            document.getElementById('emcaptcha-img').src = data.url;
        });
}
</script>
