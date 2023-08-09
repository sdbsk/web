<table style="width: 100%">
    <tr>
        <td>
            <label for="campaign_enabled">Zapnutá</label>
        </td>
        <td>
            <input type="checkbox" id="campaign_enabled" name="campaign_enabled" @if($enabled) checked="checked"@endif>
        </td>
    </tr>
    <tr>
        <td>
            <label for="campaign_description">Popis</label>
        </td>
        <td>
            <textarea id="campaign_description" name="campaign_description" rows="10" style="width: 100%">{{ $description }}</textarea>
        </td>
    </tr>
    <tr>
        <td>
            <label for="campaign_external_url">Externá URL</label>
        </td>
        <td>
            <input type="text" id="campaign_external_url" name="campaign_external_url" value="{{ $externalUrl }}">
        </td>
    </tr>
</table>
