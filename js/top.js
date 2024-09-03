document.addEventListener('DOMContentLoaded', function() {
    const timeline = document.getElementById('timeline');
    let lastPostAt = '1970-01-01 00:00:00';

    // 投稿を取得する関数
    async function fetchPosts() {
        const response = await fetch('php/top.php'+ '?lastPostAt='+lastPostAt); // PHPスクリプトのURL
        const posts = await response.json();


        for (const post of posts) {
            const userName = await fetchUserName(post.user_id); // ユーザー名を取得
            const profileImage = await fetchProfileImage(post.user_id); // プロフィール画像を取得
            const postElement = document.createElement('div');
            postElement.className = 'post';

            lastPostAt = post.created_at;

            console.log(post)

            // 画像があるかどうかを確認
            let imageTag = '';
            if (post.images) {
                imageTag = `<img src="php/${post.images}" alt="投稿画像" class="postimg">`;
            }

            postElement.innerHTML = `
                <div class="post-header">
                    <div class="avatar">${profileImage == 'img/default_profile.png' ? `<img src="${profileImage}" alt="プロフィール画像" class="profileimg">` : `<img src="php/${profileImage}" alt="プロフィール画像" class="profileimg">`}</div>
                    <div class="username">${userName}</div>
                    <div class="timestamp">${formatTimestamp(post.created_at)}</div>
                </div>
                <div class="post-content">
                    ${post.content}
                </div>
                ${imageTag ? `<div class="post-image">${imageTag}</div>` : ''}
                <div class="post-footer">
                    <div class="icon"><img src="img/comment_icon.png" alt="コメント"> 0</div>
                    <div class="icon"><img src="img/heart_icon.png" alt="ハート"> 0</div>
                </div>
            `;
            timeline.prepend(postElement);
        }

        console.log(lastPostAt);
    }

    // user_idからユーザー名を取得する関数
    async function fetchUserName(userId) {
        const response = await fetch(`php/fetch_user_names.php?user_id=${userId}`); // PHPスクリプトのURL
        const user = await response.json();
        return user.user_name || '不明';
    }

    // user_idからプロフィール画像を取得する関数
    async function fetchProfileImage(userId) {
        const response = await fetch(`php/selectimg_profile.php?user_id=${userId}`); // PHPスクリプトのURL
        const profile = await response.json();
        return profile.profile_image || 'img/default_profile.png'; // デフォルト画像のパスを指定
    }

    // タイムスタンプのフォーマット関数
    function formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        return `${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()} ${date.getHours()}:${date.getMinutes()}`;
    }

    fetchPosts(); // 初期化時に投稿を取得

    setInterval(fetchPosts, 5000);
});
