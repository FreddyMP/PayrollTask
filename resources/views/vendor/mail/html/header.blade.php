@props(['url', 'logoUrl' => null, 'companyLogo' => null])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($logoUrl)
<img src="{{ $logoUrl }}" class="logo" alt="Company Logo" style="max-height: 50px;">    
@elseif ($companyLogo)
<img src="{{ $companyLogo }}" class="logo" alt="Company Logo" style="max-height: 50px;">
@elseif (trim($slot) === 'Laravel')

@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
