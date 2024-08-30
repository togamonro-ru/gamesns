document.addEventListener('DOMContentLoaded', () => {
    let postForm = document.forms['postForm'];

    // 文字数制限のカウントを初期化
    updateCharacterCount();

    if (postForm) {
        postForm.addEventListener('submit', (event) => {
            event.preventDefault();

            let formData = new FormData(postForm);

            insertPeopleTable(formData);
        });

        // テキストエリアの入力イベントを監視して文字数を更新
        postForm.content.addEventListener('input', updateCharacterCount);
    } else {
        console.error('Form not found');
    }

    function insertPeopleTable(formData) {
        const path = 'php/post.php';

        fetch(path, {
            method: 'POST',
            body: formData,
        })
        .then((res) => res.json())
        .then((data) => {
            console.log(data);
            if (data.status === 'success') {
                window.location.href = 'top.html';
            } else {
                console.error('Submission failed:', data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }

    function updateCharacterCount() {
        const textarea = document.querySelector('.content');
        const charCount = document.getElementById('charCount');
        if (!charCount) {
            console.error('charCount element not found');
            return;
        }

        const maxLength = textarea.maxLength;
        const currentLength = textarea.value.length;

        charCount.textContent = `残り: ${maxLength - currentLength}文字`;
    }
});
