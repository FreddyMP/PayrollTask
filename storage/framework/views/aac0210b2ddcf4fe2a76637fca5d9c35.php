<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['url', 'logoUrl' => null, 'companyLogo' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['url', 'logoUrl' => null, 'companyLogo' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<tr>
<td class="header">
<a href="<?php echo new \Illuminate\Support\EncodedHtmlString($url); ?>" style="display: inline-block;">
<?php if($logoUrl): ?>
<img src="<?php echo new \Illuminate\Support\EncodedHtmlString($logoUrl); ?>" class="logo" alt="Company Logo" style="max-height: 50px;">    
<?php elseif($companyLogo): ?>
<img src="<?php echo new \Illuminate\Support\EncodedHtmlString($companyLogo); ?>" class="logo" alt="Company Logo" style="max-height: 50px;">
<?php elseif(trim($slot) === 'Laravel'): ?>

<?php else: ?>
<?php echo $slot; ?>

<?php endif; ?>
</a>
</td>
</tr>
<?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/vendor/mail/html/header.blade.php ENDPATH**/ ?>