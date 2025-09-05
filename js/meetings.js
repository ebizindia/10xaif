document.addEventListener("DOMContentLoaded", function () {
    loadMeetings();

    document.getElementById("addMeeting").addEventListener("click", function () {
        document.getElementById("meetingForm").reset();
        document.getElementById("meetingId").value = "";
        new bootstrap.Modal(document.getElementById("meetingModal")).show();
    });

    document.getElementById("meetingForm").addEventListener("submit", function (e) {
        e.preventDefault();
        saveMeeting();
    });
});

function loadMeetings() {
    common_js_funcs.callServer("getMeetings", {}, function (response) {
        let meetingsTable = document.getElementById("meetingList");
        meetingsTable.innerHTML = "";
        response.forEach(meeting => {
            meetingsTable.innerHTML += `
                <tr>
                    <td>${meeting.title}</td>
                    <td>${meeting.datetime}</td>
                    <td>${meeting.location}</td>
                    <td>${meeting.status}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editMeeting(${meeting.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteMeeting(${meeting.id})">Delete</button>
                    </td>
                </tr>
            `;
        });
    });
}