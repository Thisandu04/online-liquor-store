
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('ageForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const day = parseInt(form.day.value, 10);
        const month = parseInt(form.month.value, 10);
        const year = parseInt(form.year.value, 10);

        if (!day || !month || !year) {
            e.preventDefault();
            alert('Please fill in your complete date of birth.');
            return;
        }

        const dob = new Date(year, month - 1, day);
        const today = new Date();

        let age = today.getFullYear() - dob.getFullYear();
        const hasHadBirthdayThisYear =
            today.getMonth() > dob.getMonth() ||
            (today.getMonth() === dob.getMonth() && today.getDate() >= dob.getDate());

        if (!hasHadBirthdayThisYear) {
            age--;
        }

        if (age < 18) {
            e.preventDefault();
            alert('You must be 18 or older to enter this site.');
        }
        // If valid, form submits normally to index.php for the real server-side check
    });
});