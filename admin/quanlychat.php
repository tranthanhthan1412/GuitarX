<style>
.chat-container {
    display: flex;
    height: 75vh;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden;
}
.chat-sidebar {
    width: 300px;
    border-right: 1px solid #eee;
    background: #fdfdfd;
    display: flex;
    flex-direction: column;
}
.chat-sidebar-header {
    padding: 20px;
    background: #fff;
    border-bottom: 1px solid #eee;
    font-weight: bold;
    font-size: 1.1rem;
}
.chat-user-list {
    flex: 1;
    overflow-y: auto;
}
.chat-user-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f5f5f5;
    cursor: pointer;
    transition: background 0.2s;
}
.chat-user-item:hover, .chat-user-item.active {
    background: #f0f4f8;
}
.chat-user-name {
    font-weight: bold;
    color: #333;
    display: flex;
    justify-content: space-between;
}
.chat-user-lastmsg {
    font-size: 0.85rem;
    color: #777;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 5px;
}
.badge-unread {
    background: #e63946;
    color: #fff;
    font-size: 0.75rem;
    padding: 2px 6px;
    border-radius: 10px;
}
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #fafafa;
}
.chat-main-header {
    padding: 20px;
    background: #fff;
    border-bottom: 1px solid #eee;
    font-weight: bold;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}
.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.msg-bubble {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 15px;
    font-size: 0.95rem;
    line-height: 1.4;
    word-wrap: break-word;
}
.msg-admin {
    align-self: flex-end;
    background: #e63946;
    color: #fff;
    border-bottom-right-radius: 2px;
}
.msg-user {
    align-self: flex-start;
    background: #e9ecef;
    color: #333;
    border-bottom-left-radius: 2px;
}
.chat-input-area {
    padding: 15px 20px;
    background: #fff;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
}
.chat-input-area input {
    flex: 1;
    border: 1px solid #ddd;
    padding: 10px 15px;
    border-radius: 25px;
    outline: none;
}
.chat-input-area button {
    background: #e63946;
    color: #fff;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.chat-input-area button:hover {
    background: #c9222f;
}
.empty-chat {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #aaa;
    font-size: 1.2rem;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="font-display-md m-0">Quản lý Tin Nhắn</h2>
</div>

<div class="chat-container">
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">Danh sách Khách hàng</div>
        <div class="chat-user-list" id="userList">
            <!-- Render by JS -->
            <div class="text-center text-muted p-3">Đang tải...</div>
        </div>
    </div>
    
    <div class="chat-main" id="chatMain" style="display: none;">
        <div class="chat-main-header">
            <span class="material-symbols-outlined text-secondary-custom">person</span>
            <span id="currentUserName">Tên khách hàng</span>
        </div>
        <div class="chat-messages" id="chatMessages">
            <!-- Messages go here -->
        </div>
        <form class="chat-input-area" id="adminChatForm" onsubmit="sendReply(event)">
            <input type="text" id="adminChatInput" placeholder="Nhập câu trả lời..." autocomplete="off" required>
            <button type="submit"><span class="material-symbols-outlined">send</span></button>
        </form>
    </div>
    <div class="empty-chat" id="emptyChat">
        Chọn một khách hàng để bắt đầu trò chuyện
    </div>
</div>

<script>
let currentUserId = null;
let lastMessageCount = 0;

// Polling interval cho User List (5s)
setInterval(fetchUsers, 5000);
fetchUsers();

// Polling interval cho Messages của Current User (3s)
setInterval(fetchMessages, 3000);

function fetchUsers() {
    fetch('index.php?act=chat_api_get_users')
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            let html = '';
            if(data.data.length === 0) {
                html = '<div class="text-center text-muted p-3">Chưa có tin nhắn nào.</div>';
            } else {
                data.data.forEach(user => {
                    const activeClass = user.Ma_NguoiDung == currentUserId ? 'active' : '';
                    const unreadBadge = user.UnreadCount > 0 ? `<span class="badge-unread">${user.UnreadCount}</span>` : '';
                    html += `
                    <div class="chat-user-item ${activeClass}" onclick="selectUser(${user.Ma_NguoiDung}, '${user.TenNguoiDung}')">
                        <div class="chat-user-name">
                            ${user.TenNguoiDung}
                            ${unreadBadge}
                        </div>
                        <div class="chat-user-lastmsg">${user.LastMessage || '...'}</div>
                    </div>
                    `;
                });
            }
            document.getElementById('userList').innerHTML = html;
        }
    });
}

function selectUser(userId, userName) {
    currentUserId = userId;
    lastMessageCount = 0; // reset
    document.getElementById('currentUserName').innerText = userName;
    document.getElementById('emptyChat').style.display = 'none';
    document.getElementById('chatMain').style.display = 'flex';
    document.getElementById('chatMessages').innerHTML = '<div class="text-center text-muted mt-3">Đang tải tin nhắn...</div>';
    
    // Highlight sidebar
    fetchUsers(); // Cập nhật lại UI sidebar ngay lập tức
    fetchMessages();
}

function fetchMessages() {
    if(!currentUserId) return;
    
    fetch('index.php?act=chat_api_get_messages&user_id=' + currentUserId)
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (data.data.length !== lastMessageCount) {
                lastMessageCount = data.data.length;
                let html = '';
                data.data.forEach(msg => {
                    let className = msg.Is_Admin_Reply == 1 ? 'msg-admin' : 'msg-user';
                    html += `<div class="msg-bubble ${className}">${msg.NoiDung}</div>`;
                });
                const container = document.getElementById('chatMessages');
                container.innerHTML = html;
                container.scrollTop = container.scrollHeight;
            }
        }
    });
}

function sendReply(e) {
    e.preventDefault();
    if(!currentUserId) return;
    
    const input = document.getElementById('adminChatInput');
    const text = input.value.trim();
    if(!text) return;

    // Hiển thị ngay lên màn hình Admin
    const container = document.getElementById('chatMessages');
    container.innerHTML += `<div class="msg-bubble msg-admin">${text}</div>`;
    container.scrollTop = container.scrollHeight;
    lastMessageCount++;
    input.value = '';

    let formData = new FormData();
    formData.append('user_id', currentUserId);
    formData.append('message', text);

    fetch('index.php?act=chat_api_send', {
        method: 'POST',
        body: formData
    }).then(res => res.json()).then(data => {
        if(data.status === 'success') {
            fetchUsers(); // Cập nhật last message
        }
    });
}
</script>
