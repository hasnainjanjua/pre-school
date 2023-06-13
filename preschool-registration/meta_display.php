<div class="pr-form">
    <style>
        .pr-form{
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .pf-field input:not([type='radio']){
            width: 100%;
            height: 40px;
            margin: 5px 0;
        }
        label{
            font-size: 14px;
            font-weight: 500;
        }
        .location-text{
            font-size: 14px;
            font-weight: 700;
        }
    </style>
    <div class="pf-field">
        <label for="pr-name">Name of Pre-School</label>
        <input name="pr-name" id="pr-name" type="text" value="<?= esc_attr($pr_name) ?>">
    </div><div class="pf-field">
        <label for="pr-address">Address</label>
        <input name="pr-address" id="pr-address" type="text" value="<?= esc_attr($pr_address) ?>">
    </div><div class="pf-field">
        <label for="pr-time">Time of Registration</label>
        <input name="pr-time" id="pr-time" type="date" value="<?= esc_attr($pr_time) ?>">
    </div><div class="pf-field">
            <p class="location-text">Location accepting registrations?</p>
            <label for="yes">Yes</label>
            <input type="radio" id="yes" name="pr-location" value="yes" <?php if($pr_location == 'yes'){echo 'checked';} ?>>
            <label for="no">No</label>
            <input type="radio" id="no" name="pr-location" value="no" <?php if($pr_location == 'no'){echo 'checked';} ?>>
    </div>
</div>