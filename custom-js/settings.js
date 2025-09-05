const Settings = {
    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        $('.edit-setting').on('click', function() {
            const settingId = $(this).data('id');
            const currentValue = $(this).data('value');
            
            $('#setting_id').val(settingId);
            $('#setting_value').val(currentValue);
            $('#editSettingModal').modal('show');
        });

        $('#saveSettingBtn').on('click', function() {
            Settings.saveSetting();
        });
    },

    saveSetting: function() {
        const settingId = $('#setting_id').val();
        const settingValue = $('#setting_value').val();

        if (!settingValue.trim()) {
            alert('Please enter a value');
            return;
        }

        const data = {
            action: 'update_setting',
            setting_id: settingId,
            setting_value: settingValue
        };

        common_js_funcs.callServer('settings', data, function(response) {
            if (response.status === 'success') {
                $(`span.setting-value[data-id="${settingId}"]`).text(settingValue);
                $(`button.edit-setting[data-id="${settingId}"]`).data('value', settingValue);
                $('#editSettingModal').modal('hide');
            } else {
                alert(response.message || 'Error updating setting');
            }
        });
    },

    handleHashChange: function() {
        const hash = window.location.hash;
        // Handle any hash-based navigation if needed
    }
};

// Export for use in other modules if needed
window.Settings = Settings;