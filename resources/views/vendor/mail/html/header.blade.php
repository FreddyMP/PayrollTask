@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://lh3.googleusercontent.com/ogw/AF2bZyhqttwLfIh-RViD3_3_0hg-gmZMPbabO4yad2ZAD92HTyA=s32-c-mo" class="logo" alt="Laravel Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
