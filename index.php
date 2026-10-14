<?php
declare(strict_types=1);
session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#f6f5f2">
  <title>MyAI — your personal assistant</title>
  <style>
    :root {
      color-scheme: light;
      --ink: #242b29;
      --muted: #747d78;
      --line: #e9e8e3;
      --paper: #fff;
      --canvas: #f6f5f2;
      --green: #456b5d;
      --green-dark: #315247;
      --green-soft: #eaf0ec;
      --gold: #c6a36e;
      --shadow: 0 16px 50px rgba(31, 43, 38, .08);
    }
    * { box-sizing: border-box; }
    body { margin: 0; background: var(--canvas); color: var(--ink); font: 15px/1.5 Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
    button, input, textarea { font: inherit; }
    button { cursor: pointer; }
    .app { display: flex; min-height: 100vh; }
    .sidebar { width: 274px; flex: 0 0 274px; display: flex; flex-direction: column; padding: 24px 17px 16px; background: #f0efeb; border-right: 1px solid #e8e7e1; }
    .brand { display: flex; align-items: center; gap: 11px; padding: 2px 10px 25px; }
    .brand-mark { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 12px; color: #fff; background: var(--green); box-shadow: 0 5px 12px #456b5d33; }
    .brand-mark svg { width: 19px; height: 19px; }
    .brand-name { font-size: 16px; font-weight: 700; letter-spacing: -.04em; }
    .brand-note { margin-top: -2px; color: var(--muted); font-size: 11px; }
    .new-chat { width: 100%; display: flex; align-items: center; gap: 10px; padding: 11px 13px; color: #fff; background: var(--green); border: 0; border-radius: 10px; font-weight: 600; box-shadow: 0 5px 12px #456b5d22; transition: background .15s, transform .15s; }
    .new-chat:hover { background: var(--green-dark); transform: translateY(-1px); }
    .new-chat svg { width: 17px; height: 17px; }
    .section-label { margin: 26px 10px 9px; color: #929994; font-size: 10px; font-weight: 700; letter-spacing: .11em; text-transform: uppercase; }
    .chat-list { flex: 1; overflow: auto; display: flex; flex-direction: column; gap: 3px; }
    .chat-list-empty { padding: 12px 10px; color: #929994; font-size: 12px; }
    .chat-item { display: flex; align-items: center; gap: 8px; width: 100%; padding: 9px 10px; color: #59615c; background: transparent; border: 0; border-radius: 8px; text-align: left; font-size: 13px; }
    .chat-item:hover, .chat-item.active { color: var(--ink); background: #e6e6e0; }
    .chat-item span { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; flex: 1; }
    .chat-item svg { width: 15px; height: 15px; flex: 0 0 auto; color: #89918b; }
    .chat-remove { width: 23px; height: 23px; display: none; place-items: center; border: 0; border-radius: 6px; color: #717a74; background: transparent; }
    .chat-item:hover .chat-remove { display: grid; }
    .chat-remove:hover { background: #d9dbd4; color: #9a4841; }
    .sidebar-bottom { border-top: 1px solid #e0e0d9; padding-top: 14px; }
    .profile-button { display: flex; align-items: center; gap: 10px; width: 100%; padding: 8px; border: 0; border-radius: 9px; background: transparent; text-align: left; }
    .profile-button:hover { background: #e6e6e0; }
    .avatar { width: 32px; height: 32px; display: grid; place-items: center; border-radius: 50%; color: #52685d; background: #dce6de; font-weight: 700; font-size: 12px; }
    .profile-copy { min-width: 0; flex: 1; }
    .profile-name { overflow: hidden; color: #414a45; font-size: 12px; font-weight: 600; white-space: nowrap; text-overflow: ellipsis; }
    .profile-status { color: #929994; font-size: 10px; }
    .profile-button > svg { width: 16px; height: 16px; color: #87908a; }
    .main { min-width: 0; flex: 1; display: flex; flex-direction: column; }
    .topbar { height: 68px; display: flex; align-items: center; justify-content: space-between; padding: 0 34px; border-bottom: 1px solid var(--line); background: rgba(255,255,255,.54); }
    .workspace-label { display: flex; align-items: center; gap: 9px; color: var(--muted); font-size: 12px; }
    .workspace-label .dot { width: 7px; height: 7px; border-radius: 50%; background: #86a18f; box-shadow: 0 0 0 3px #86a18f22; }
    .top-actions { display: flex; align-items: center; gap: 8px; }
    .icon-button, .settings-button { height: 36px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 11px; color: #58615c; background: transparent; border: 1px solid transparent; border-radius: 8px; font-size: 12px; }
    .icon-button:hover, .settings-button:hover { background: #fff; border-color: var(--line); }
    .icon-button svg, .settings-button svg { width: 16px; height: 16px; }
    .content { width: 100%; max-width: 930px; min-height: calc(100vh - 68px); display: flex; flex-direction: column; margin: 0 auto; padding: 42px 42px 28px; }
    .welcome { margin: auto 0 32px; padding-bottom: 9px; }
    .welcome-kicker { display: inline-flex; align-items: center; gap: 7px; margin-bottom: 18px; padding: 6px 10px; color: #577367; background: var(--green-soft); border-radius: 30px; font-size: 11px; font-weight: 600; }
    .welcome-kicker svg { width: 13px; height: 13px; }
    h1 { max-width: 650px; margin: 0; font-family: Georgia, "Times New Roman", serif; font-size: clamp(34px, 5vw, 52px); line-height: 1.08; font-weight: 400; letter-spacing: -.045em; }
    h1 em { color: var(--green); font-weight: 400; }
    .welcome-copy { max-width: 535px; margin: 17px 0 26px; color: var(--muted); font-size: 14px; line-height: 1.75; }
    .prompt-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; max-width: 720px; }
    .prompt-card { min-height: 68px; display: flex; align-items: flex-start; gap: 11px; padding: 13px 14px; color: #4f5953; background: rgba(255,255,255,.68); border: 1px solid #e9e8e3; border-radius: 11px; text-align: left; transition: background .15s, border-color .15s, transform .15s; }
    .prompt-card:hover { background: #fff; border-color: #d3ddd5; transform: translateY(-1px); }
    .prompt-icon { width: 25px; height: 25px; display: grid; flex: 0 0 auto; place-items: center; color: var(--green); background: #edf2ee; border-radius: 7px; }
    .prompt-icon svg { width: 14px; height: 14px; }
    .prompt-text { padding-top: 2px; font-size: 12px; line-height: 1.45; }
    .conversation { display: none; width: 100%; max-width: 740px; margin: 0 auto auto; padding-bottom: 24px; }
    .conversation.visible { display: block; }
    .message { display: flex; gap: 13px; margin: 20px 0 27px; animation: enter .2s ease-out; }
    @keyframes enter { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .message-avatar { width: 30px; height: 30px; display: grid; place-items: center; flex: 0 0 auto; border-radius: 10px; color: #fff; background: var(--green); }
    .message-avatar.user { color: #606862; background: #e5e7e2; }
    .message-avatar svg { width: 16px; height: 16px; }
    .message-body { min-width: 0; flex: 1; padding-top: 2px; }
    .message-name { margin-bottom: 5px; color: #727b75; font-size: 11px; font-weight: 600; }
    .message-text { color: #303936; font-size: 14px; line-height: 1.75; white-space: pre-wrap; overflow-wrap: anywhere; }
    .message.user-message .message-text { color: #4b544e; }
    .typing { display: inline-flex; gap: 4px; padding: 8px 2px; }
    .typing i { width: 5px; height: 5px; display: block; border-radius: 50%; background: #90a298; animation: pulse 1s infinite; }
    .typing i:nth-child(2) { animation-delay: .15s; } .typing i:nth-child(3) { animation-delay: .3s; }
    @keyframes pulse { 0%, 60%, 100% { opacity: .4; transform: translateY(0); } 30% { opacity: 1; transform: translateY(-3px); } }
    .composer-wrap { position: sticky; bottom: 0; margin-top: auto; padding-top: 13px; background: linear-gradient(180deg, rgba(246,245,242,0), var(--canvas) 20%); }
    .composer { max-width: 740px; margin: 0 auto; padding: 8px 9px 8px 17px; background: #fff; border: 1px solid #e4e5df; border-radius: 14px; box-shadow: 0 7px 22px rgba(37,46,40,.05); transition: border-color .15s, box-shadow .15s; }
    .composer:focus-within { border-color: #b8c9be; box-shadow: 0 0 0 3px #456b5d10, 0 7px 22px rgba(37,46,40,.05); }
    .composer textarea { width: 100%; min-height: 43px; max-height: 180px; display: block; resize: none; padding: 11px 4px 7px 0; color: var(--ink); background: transparent; border: 0; outline: 0; font-size: 14px; line-height: 1.5; }
    .composer textarea::placeholder { color: #a2a9a4; }
    .composer-footer { display: flex; align-items: center; justify-content: space-between; min-height: 32px; }
    .composer-hint { color: #9ba29d; font-size: 10px; }
    .send-button { width: 32px; height: 32px; display: grid; place-items: center; color: #fff; background: var(--green); border: 0; border-radius: 9px; transition: background .15s, transform .15s; }
    .send-button:hover { background: var(--green-dark); transform: translateY(-1px); }
    .send-button:disabled { cursor: wait; opacity: .55; transform: none; }
    .send-button svg { width: 16px; height: 16px; }
    .privacy-note { margin: 11px auto 0; color: #a1a7a2; text-align: center; font-size: 10px; }
    .setup-callout { display: none; max-width: 740px; margin: 0 auto 12px; padding: 12px 14px; color: #685a43; background: #f4efe5; border: 1px solid #ece2cf; border-radius: 10px; font-size: 12px; }
    .setup-callout.visible { display: block; }
    .setup-callout button { padding: 0; color: #456b5d; background: none; border: 0; font-weight: 700; text-decoration: underline; }
    .modal-backdrop { position: fixed; z-index: 10; inset: 0; display: none; align-items: center; justify-content: center; padding: 20px; background: rgba(31,39,35,.36); backdrop-filter: blur(4px); }
    .modal-backdrop.open { display: flex; }
    .modal { width: min(100%, 540px); max-height: min(90vh, 740px); overflow: auto; padding: 25px; background: #fff; border: 1px solid #efeee9; border-radius: 16px; box-shadow: var(--shadow); animation: enter .18s ease-out; }
    .modal-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 18px; margin-bottom: 21px; }
    .modal-header h2 { margin: 0; font-family: Georgia, "Times New Roman", serif; font-size: 26px; font-weight: 400; letter-spacing: -.03em; }
    .modal-header p { margin: 5px 0 0; color: var(--muted); font-size: 12px; }
    .close-button { width: 31px; height: 31px; display: grid; place-items: center; color: #7b837e; background: #f6f6f3; border: 0; border-radius: 8px; }
    .close-button:hover { background: #edeee9; }
    .close-button svg { width: 16px; height: 16px; }
    .field { margin: 0 0 15px; }
    .field label { display: block; margin-bottom: 6px; color: #515a55; font-size: 11px; font-weight: 700; }
    .field input, .field textarea { width: 100%; padding: 10px 11px; color: var(--ink); background: #fcfcfa; border: 1px solid #e4e6df; border-radius: 8px; outline: none; font-size: 12px; }
    .field input:focus, .field textarea:focus { border-color: #abc0b2; box-shadow: 0 0 0 3px #456b5d0e; }
    .field textarea { min-height: 76px; resize: vertical; line-height: 1.5; }
    .field small { display: block; margin-top: 5px; color: #8d9690; font-size: 10px; line-height: 1.5; }
    .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .settings-divider { height: 1px; margin: 19px 0; background: var(--line); }
    .section-heading { margin: 0 0 13px; color: #77817a; font-size: 10px; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; }
    .key-row { display: flex; gap: 8px; }
    .key-row input { flex: 1; min-width: 0; }
    .forget-button { padding: 0 10px; color: #777e78; background: #f4f4f1; border: 1px solid #e8e9e3; border-radius: 8px; font-size: 11px; }
    .forget-button:hover { color: #9a4841; background: #faf0ee; }
    .key-status { min-height: 15px; margin-top: 6px; color: #71877a; font-size: 10px; }
    .modal-footer { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-top: 22px; }
    .save-status { color: #748079; font-size: 11px; }
    .primary-button { padding: 10px 16px; color: #fff; background: var(--green); border: 0; border-radius: 8px; font-size: 12px; font-weight: 600; }
    .primary-button:hover { background: var(--green-dark); }
    .primary-button:disabled { opacity: .6; cursor: wait; }
    .toast { position: fixed; z-index: 20; left: 50%; bottom: 26px; padding: 11px 15px; color: #fff; background: #303a35; border-radius: 9px; box-shadow: var(--shadow); font-size: 12px; opacity: 0; pointer-events: none; transform: translate(-50%, 10px); transition: .2s; }
    .toast.show { opacity: 1; transform: translate(-50%, 0); }
    @media (max-width: 760px) {
      .sidebar { width: 63px; flex-basis: 63px; align-items: center; padding: 17px 8px 12px; }
      .brand { padding: 0 0 19px; }
      .brand > div:last-child, .new-chat span, .section-label, .chat-list, .profile-copy, .profile-button > svg { display: none; }
      .new-chat { width: 39px; height: 39px; justify-content: center; padding: 0; }
      .sidebar-bottom { width: 100%; display: flex; justify-content: center; padding-top: 10px; }
      .profile-button { justify-content: center; padding: 4px 0; }
      .topbar { height: 58px; padding: 0 16px; }
      .content { min-height: calc(100vh - 58px); padding: 35px 20px 22px; }
      .welcome { margin-bottom: 22px; }
      .prompt-grid { grid-template-columns: 1fr; gap: 8px; }
      .prompt-card { min-height: 56px; }
      .settings-button span { display: none; }
      .settings-button { width: 36px; padding: 0; }
    }
    @media (max-width: 430px) {
      .workspace-label { font-size: 11px; }
      .content { padding: 29px 15px 19px; }
      .welcome-copy { font-size: 13px; }
      .field-row { grid-template-columns: 1fr; gap: 0; }
      .modal { padding: 20px; }
      .composer-hint { font-size: 9px; }
    }
  </style>
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3.5c1.2 4.1 2.4 5.3 6.5 6.5-4.1 1.2-5.3 2.4-6.5 6.5-1.2-4.1-2.4-5.3-6.5-6.5 4.1-1.2 5.3-2.4 6.5-6.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M18.5 15.5c.5 1.7 1.3 2.5 3 3-1.7.5-2.5 1.3-3 3-.5-1.7-1.3-2.5-3-3 1.7-.5 2.5-1.3 3-3Z" fill="currentColor"/></svg></div>
      <div><div class="brand-name">MyAI</div><div class="brand-note">your personal assistant</div></div>
    </div>
    <button class="new-chat" id="newChatButton"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg><span>New conversation</span></button>
    <div class="section-label">Your conversations</div>
    <div class="chat-list" id="chatList"></div>
    <div class="sidebar-bottom">
      <button class="profile-button" id="profileButton" aria-label="Open personal AI settings">
        <div class="avatar" id="avatarInitial">M</div>
        <div class="profile-copy"><div class="profile-name" id="sidebarAssistantName">My Assistant</div><div class="profile-status" id="connectionStatus">Set up your AI</div></div>
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="1.6"/><path d="m19.4 15 .1.1a1.8 1.8 0 1 1-2.5 2.5l-.1-.1a1.8 1.8 0 0 0-3.1 1.3v.2a1.8 1.8 0 1 1-3.6 0v-.2a1.8 1.8 0 0 0-3.1-1.3l-.1.1a1.8 1.8 0 1 1-2.5-2.5l.1-.1A1.8 1.8 0 0 0 3.3 12H3a1.8 1.8 0 1 1 0-3.6h.2A1.8 1.8 0 0 0 4.5 5.3l-.1-.1A1.8 1.8 0 1 1 7 2.7l.1.1a1.8 1.8 0 0 0 3.1-1.3v-.2a1.8 1.8 0 1 1 3.6 0v.2a1.8 1.8 0 0 0 3.1 1.3l.1-.1a1.8 1.8 0 1 1 2.5 2.5l-.1.1A1.8 1.8 0 0 0 20.7 8h.2a1.8 1.8 0 1 1 0 3.6h-.2a1.8 1.8 0 0 0-1.3 3.4Z" stroke="currentColor" stroke-width="1.2" transform="translate(1.5 2.8) scale(.87)"/></svg>
      </button>
    </div>
  </aside>
  <main class="main">
    <header class="topbar">
      <div class="workspace-label"><span class="dot"></span><span>Personal workspace</span></div>
      <div class="top-actions">
        <button class="icon-button" id="exportButton" title="Export this conversation" aria-label="Export this conversation"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 15v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
        <button class="settings-button" id="settingsButton"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="1.6"/><path d="M19.4 15a1.8 1.8 0 0 0 1-1.6v-.2a1.8 1.8 0 0 0-1-1.6l-.2-.1a1.8 1.8 0 0 1 0-3.1l.2-.1a1.8 1.8 0 0 0-1.8-3.1l-.2.1a1.8 1.8 0 0 1-2.7-1.6v-.2a1.8 1.8 0 0 0-3.6 0v.2a1.8 1.8 0 0 1-2.7 1.6l-.2-.1A1.8 1.8 0 0 0 6.4 8.3l.2.1a1.8 1.8 0 0 1 0 3.1l-.2.1a1.8 1.8 0 0 0 1.8 3.1l.2-.1a1.8 1.8 0 0 1 2.7 1.6v.2a1.8 1.8 0 0 0 3.6 0v-.2a1.8 1.8 0 0 1 2.7-1.6l.2.1" stroke="currentColor" stroke-width="1.3" transform="translate(1.5 1.2) scale(.87)"/></svg><span>Personalize</span></button>
      </div>
    </header>
    <section class="content">
      <div class="welcome" id="welcome">
        <div class="welcome-kicker"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3.5c1.2 4.1 2.4 5.3 6.5 6.5-4.1 1.2-5.3 2.4-6.5 6.5-1.2-4.1-2.4-5.3-6.5-6.5 4.1-1.2 5.3-2.4 6.5-6.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg> A little space, just for you</div>
        <h1>Good to have you here.<br><em>What’s on your mind?</em></h1>
        <p class="welcome-copy">Your personal AI is ready to think things through with you, help you get things done, or just make a little more sense of the day.</p>
        <div class="prompt-grid">
          <button class="prompt-card" data-prompt="Help me organize my priorities for this week. Ask me what you need to know first."><span class="prompt-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M5 6h14M5 12h14M5 18h8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></span><span class="prompt-text">Help me find a little more focus this week</span></button>
          <button class="prompt-card" data-prompt="I have a decision on my mind. Help me think it through by asking thoughtful questions."><span class="prompt-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M12 18.5v-1.8c0-2.7 4.5-3.1 4.5-7.1a4.5 4.5 0 0 0-9 0M10.5 21h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></span><span class="prompt-text">Think through a decision with me</span></button>
          <button class="prompt-card" data-prompt="Can you help me make a simple plan for a goal I have? Start by asking about the goal."><span class="prompt-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M4.5 19.5V5m0 14.5h15M8 15l3-3 2.5 2L19 8.5m0 0h-4m4 0v4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="prompt-text">Turn a goal into a doable plan</span></button>
          <button class="prompt-card" data-prompt="I want to learn something new. Suggest a few interesting topics and help me pick one."><span class="prompt-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3.5a6 6 0 0 0-3.8 10.6c.8.7 1.3 1.4 1.4 2.4h4.8c.1-1 .6-1.7 1.4-2.4A6 6 0 0 0 12 3.5ZM9.8 20.5h4.4m-4.8-4h5.2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="prompt-text">Explore something I’ve been curious about</span></button>
        </div>
      </div>
      <div class="conversation" id="conversation" aria-live="polite"></div>
      <div class="composer-wrap">
        <div class="setup-callout" id="setupCallout">Connect a model to start chatting. <button id="setupLink">Open your settings</button></div>
        <form class="composer" id="composerForm">
          <textarea id="messageInput" rows="1" placeholder="Write whatever’s on your mind…" aria-label="Message your personal AI"></textarea>
          <div class="composer-footer"><span class="composer-hint">Enter to send · Shift + Enter for a new line</span><button type="submit" class="send-button" id="sendButton" aria-label="Send message"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 19V5m0 0L6 11m6-6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></button></div>
        </form>
        <div class="privacy-note">Your conversations stay in this browser. Your AI’s replies come from the model you connect.</div>
      </div>
    </section>
  </main>
</div>

<div class="modal-backdrop" id="settingsModal" role="dialog" aria-modal="true" aria-labelledby="settingsTitle">
  <div class="modal">
    <div class="modal-header"><div><h2 id="settingsTitle">Make it yours</h2><p>Shape your assistant and connect the model that powers it.</p></div><button class="close-button" id="closeSettings" aria-label="Close settings"><svg viewBox="0 0 24 24" fill="none"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></button></div>
    <form id="settingsForm">
      <div class="section-heading">Your assistant</div>
      <div class="field"><label for="assistantName">Name</label><input id="assistantName" maxlength="40" placeholder="e.g. Alex, Sage, or My Assistant"></div>
      <div class="field"><label for="aboutYou">A little about you</label><textarea id="aboutYou" maxlength="1200" placeholder="What do you do? What are you working toward? Share only what you’re comfortable including in prompts."></textarea><small>This is saved in this browser and included with your chats to help personalize replies.</small></div>
      <div class="field"><label for="customInstructions">How should your assistant help?</label><textarea id="customInstructions" maxlength="2000" placeholder="For example: Be warm and direct. Keep answers practical. Ask before making assumptions."></textarea></div>
      <div class="settings-divider"></div>
      <div class="section-heading">Connect an AI model</div>
      <div class="field-row"><div class="field"><label for="modelName">Model name</label><input id="modelName" autocomplete="off" placeholder="Enter the model ID"></div><div class="field"><label for="apiEndpoint">API endpoint</label><input id="apiEndpoint" autocomplete="url" placeholder="https://api.openai.com/v1/chat/completions"></div></div>
      <div class="field"><label for="apiKey">API key</label><div class="key-row"><input id="apiKey" type="password" autocomplete="new-password" placeholder="Paste your API key"><button type="button" class="forget-button" id="forgetKey">Forget key</button></div><div class="key-status" id="keyStatus"></div><small>Your key is held in this app’s PHP session, not in browser storage. Anyone with access to this local server may be able to use its active session.</small></div>
      <div class="modal-footer"><span class="save-status" id="saveStatus"></span><button class="primary-button" id="saveSettings" type="submit">Save settings</button></div>
    </form>
  </div>
</div>
<div class="toast" id="toast" role="status"></div>

<script>
(() => {
  const API = 'api.php';
  const STORAGE_KEY = 'myai.workspace.v1';
  const defaults = { assistantName: 'My Assistant', aboutYou: '', instructions: '' };
  let profile = { ...defaults };
  let conversations = [];
  let activeId = null;
  let configured = false;
  let sending = false;
  let keyPresent = false;
  let toastTimer;

  const $ = (id) => document.getElementById(id);
  const els = {
    welcome: $('welcome'), conversation: $('conversation'), chatList: $('chatList'), composer: $('composerForm'), input: $('messageInput'), send: $('sendButton'),
    modal: $('settingsModal'), settingsForm: $('settingsForm'), name: $('assistantName'), about: $('aboutYou'), instructions: $('customInstructions'), model: $('modelName'), endpoint: $('apiEndpoint'), key: $('apiKey'), keyStatus: $('keyStatus'), saveStatus: $('saveStatus'), save: $('saveSettings'),
    status: $('connectionStatus'), sidebarName: $('sidebarAssistantName'), avatar: $('avatarInitial'), callout: $('setupCallout'), toast: $('toast')
  };

  function loadLocal() {
    try {
      const value = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
      profile = { ...defaults, ...(value.profile || {}) };
      conversations = Array.isArray(value.conversations) ? value.conversations.filter(c => c && typeof c.id === 'string' && Array.isArray(c.messages)) : [];
      activeId = conversations.some(c => c.id === value.activeId) ? value.activeId : null;
    } catch (_) { profile = { ...defaults }; conversations = []; activeId = null; }
  }

  function persist() {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify({ profile, conversations, activeId })); }
    catch (_) { showToast('This browser could not save your conversation.'); }
  }

  async function api(action, data = {}) {
    const response = await fetch(API, {
      method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ action, ...data }), credentials: 'same-origin'
    });
    const result = await response.json().catch(() => ({}));
    if (!response.ok) { const error = new Error(result.error || 'Something went wrong.'); error.status = response.status; throw error; }
    return result;
  }

  function showToast(message) {
    els.toast.textContent = message;
    els.toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => els.toast.classList.remove('show'), 2800);
  }

  function activeConversation() { return conversations.find(c => c.id === activeId) || null; }
  function ensureConversation() {
    let chat = activeConversation();
    if (!chat) {
      chat = { id: (crypto.randomUUID ? crypto.randomUUID() : String(Date.now()) + Math.random().toString(16).slice(2)), title: 'New conversation', messages: [], updatedAt: Date.now() };
      conversations.unshift(chat); activeId = chat.id;
    }
    return chat;
  }

  function iconChat() { return '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H5l1.5-3.4A7.5 7.5 0 1 1 20 11.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>'; }

  function renderSidebar() {
    els.chatList.replaceChildren();
    if (!conversations.length) {
      const empty = document.createElement('div'); empty.className = 'chat-list-empty'; empty.textContent = 'Your chats will appear here'; els.chatList.append(empty);
    } else {
      conversations.slice().sort((a, b) => (b.updatedAt || 0) - (a.updatedAt || 0)).forEach(chat => {
        const row = document.createElement('button'); row.className = `chat-item${chat.id === activeId ? ' active' : ''}`; row.type = 'button';
        row.innerHTML = iconChat();
        const label = document.createElement('span'); label.textContent = chat.title || 'Conversation'; row.append(label);
        const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'chat-remove'; remove.setAttribute('aria-label', 'Delete conversation'); remove.innerHTML = '<svg viewBox="0 0 24 24" fill="none"><path d="M5 7h14m-12 0 .8 13h8.4L17 7M9 7V4h6v3m-5 4v5m4-5v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        remove.addEventListener('click', (event) => { event.stopPropagation(); deleteConversation(chat.id); });
        row.append(remove); row.addEventListener('click', () => { activeId = chat.id; persist(); render(); }); els.chatList.append(row);
      });
    }
    els.sidebarName.textContent = profile.assistantName || defaults.assistantName;
    els.avatar.textContent = (profile.assistantName || 'M').trim().charAt(0).toUpperCase() || 'M';
    els.status.textContent = configured ? (keyPresent ? 'Model connected' : 'Add your API key') : 'Set up your AI';
  }

  function renderMessage(message) {
    const row = document.createElement('article'); row.className = `message ${message.role === 'user' ? 'user-message' : 'assistant-message'}`;
    const avatar = document.createElement('div'); avatar.className = `message-avatar${message.role === 'user' ? ' user' : ''}`;
    avatar.innerHTML = message.role === 'user' ? '<svg viewBox="0 0 24 24" fill="none"><path d="M20 20a8 8 0 0 0-16 0m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>' : '<svg viewBox="0 0 24 24" fill="none"><path d="M12 3.5c1.2 4.1 2.4 5.3 6.5 6.5-4.1 1.2-5.3 2.4-6.5 6.5-1.2-4.1-2.4-5.3-6.5-6.5 4.1-1.2 5.3-2.4 6.5-6.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>';
    const body = document.createElement('div'); body.className = 'message-body';
    const name = document.createElement('div'); name.className = 'message-name'; name.textContent = message.role === 'user' ? 'You' : (profile.assistantName || defaults.assistantName);
    const text = document.createElement('div'); text.className = 'message-text'; text.textContent = message.content || '';
    body.append(name, text); row.append(avatar, body); return row;
  }

  function render() {
    renderSidebar();
    const chat = activeConversation();
    const hasMessages = !!(chat && chat.messages.length);
    els.welcome.style.display = hasMessages ? 'none' : 'block';
    els.conversation.classList.toggle('visible', hasMessages);
    els.conversation.replaceChildren();
    if (hasMessages) {
      chat.messages.forEach(message => els.conversation.append(renderMessage(message)));
      els.conversation.lastElementChild?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }
  }

  function setBusy(value) {
    sending = value; els.send.disabled = value; els.input.disabled = value;
    if (value) {
      const row = document.createElement('article'); row.className = 'message assistant-message'; row.id = 'typingIndicator';
      const avatar = document.createElement('div'); avatar.className = 'message-avatar'; avatar.innerHTML = '<svg viewBox="0 0 24 24" fill="none"><path d="M12 3.5c1.2 4.1 2.4 5.3 6.5 6.5-4.1 1.2-5.3 2.4-6.5 6.5-1.2-4.1-2.4-5.3-6.5-6.5 4.1-1.2 5.3-2.4 6.5-6.5Z" stroke="currentColor" stroke-width="1.6"/></svg>';
      const body = document.createElement('div'); body.className = 'message-body'; const name = document.createElement('div'); name.className = 'message-name'; name.textContent = profile.assistantName || defaults.assistantName;
      const dots = document.createElement('div'); dots.className = 'typing'; dots.innerHTML = '<i></i><i></i><i></i>'; body.append(name, dots); row.append(avatar, body); els.conversation.append(row); row.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    } else { document.getElementById('typingIndicator')?.remove(); els.input.disabled = false; els.input.focus(); }
  }

  async function sendMessage(text) {
    const content = text.trim(); if (!content || sending) return;
    const chat = ensureConversation();
    chat.messages.push({ role: 'user', content });
    if (chat.messages.filter(m => m.role === 'user').length === 1) chat.title = content.length > 35 ? content.slice(0, 35).trim() + '…' : content;
    chat.updatedAt = Date.now(); persist(); render(); els.input.value = ''; resizeInput();
    if (!configured || !keyPresent) {
      els.callout.classList.add('visible'); showSettings();
      showToast('Add a model name and API key to get a reply.');
      return;
    }
    await requestReply(chat);
  }

  async function requestReply(chat) {
    if (!chat || sending) return;
    setBusy(true);
    try {
      const system = [
        `You are ${profile.assistantName || 'a personal AI assistant'}, a helpful personal AI. Be thoughtful, clear, and practical.`,
        profile.aboutYou ? `About the person you are helping:\n${profile.aboutYou}` : '',
        profile.instructions ? `Personal instructions:\n${profile.instructions}` : ''
      ].filter(Boolean).join('\n\n');
      const result = await api('chat', { system, messages: chat.messages.slice(-30).map(m => ({ role: m.role, content: m.content })) });
      chat.messages.push({ role: 'assistant', content: result.reply }); chat.updatedAt = Date.now(); persist(); render();
    } catch (error) {
      if (error.status === 409) { configured = true; keyPresent = false; els.callout.classList.add('visible'); showSettings(); }
      const chatAgain = activeConversation();
      if (chatAgain) { chatAgain.messages.push({ role: 'assistant', content: `I couldn’t get a reply just now. ${error.message || 'Please check your model settings and try again.'}` }); persist(); render(); }
    } finally { setBusy(false); }
  }

  function newChat() { activeId = null; persist(); render(); els.input.focus(); }
  function deleteConversation(id) {
    conversations = conversations.filter(c => c.id !== id);
    if (activeId === id) activeId = null;
    persist(); render();
  }

  function openSettings() {
    els.name.value = profile.assistantName || '';
    els.about.value = profile.aboutYou || '';
    els.instructions.value = profile.instructions || '';
    els.key.value = '';
    els.key.placeholder = keyPresent ? 'Key saved in this session — leave blank to keep it' : 'Paste your API key';
    els.keyStatus.textContent = keyPresent ? 'An API key is active in this browser session.' : 'No API key is connected yet.';
    els.model.value = window.serverConfig?.model || '';
    els.endpoint.value = window.serverConfig?.endpoint || 'https://api.openai.com/v1/chat/completions';
    els.saveStatus.textContent = '';
    els.modal.classList.add('open');
    setTimeout(() => els.name.focus(), 30);
  }
  function closeSettings() { els.modal.classList.remove('open'); }
  function showSettings() { openSettings(); }

  function resizeInput() { els.input.style.height = 'auto'; els.input.style.height = Math.min(els.input.scrollHeight, 180) + 'px'; }

  async function saveSettings(event) {
    event.preventDefault(); els.save.disabled = true; els.saveStatus.textContent = 'Saving…';
    const nextProfile = { assistantName: els.name.value.trim() || defaults.assistantName, aboutYou: els.about.value.trim(), instructions: els.instructions.value.trim() };
    try {
      const result = await api('configure', { model: els.model.value.trim(), endpoint: els.endpoint.value.trim(), apiKey: els.key.value.trim() });
      profile = nextProfile; configured = result.configured; keyPresent = result.keyPresent;
      window.serverConfig = result.config;
      els.key.value = '';
      persist(); render(); els.keyStatus.textContent = keyPresent ? 'An API key is active in this browser session.' : 'No API key is connected yet.';
      els.key.placeholder = keyPresent ? 'Key saved in this session — leave blank to keep it' : 'Paste your API key';
      els.saveStatus.textContent = 'Settings saved.'; showToast('Settings saved.');
      if (configured && keyPresent) {
        setTimeout(closeSettings, 450);
        const chat = activeConversation();
        if (chat?.messages.at(-1)?.role === 'user') await requestReply(chat);
      }
    } catch (error) { els.saveStatus.textContent = error.message || 'Could not save settings.'; }
    finally { els.save.disabled = false; }
  }

  async function forgetKey() {
    try {
      const result = await api('forget_key'); keyPresent = result.keyPresent; configured = result.configured; window.serverConfig = result.config;
      els.key.value = ''; els.keyStatus.textContent = 'API key forgotten for this session.'; els.key.placeholder = 'Paste your API key'; render(); showToast('API key removed from this session.');
    } catch (error) { els.keyStatus.textContent = error.message; }
  }

  function exportConversation() {
    const chat = activeConversation();
    if (!chat || !chat.messages.length) { showToast('There is no conversation to export yet.'); return; }
    const lines = [`# ${chat.title || 'Conversation'}`, ''];
    chat.messages.forEach(m => lines.push(`## ${m.role === 'user' ? 'You' : profile.assistantName}`, '', m.content, ''));
    const blob = new Blob([lines.join('\n')], { type: 'text/markdown;charset=utf-8' });
    const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = 'myai-conversation.md'; link.click(); URL.revokeObjectURL(link.href);
  }

  $('newChatButton').addEventListener('click', newChat);
  $('settingsButton').addEventListener('click', openSettings);
  $('profileButton').addEventListener('click', openSettings);
  $('closeSettings').addEventListener('click', closeSettings);
  $('setupLink').addEventListener('click', openSettings);
  $('forgetKey').addEventListener('click', forgetKey);
  $('exportButton').addEventListener('click', exportConversation);
  $('settingsForm').addEventListener('submit', saveSettings);
  $('composerForm').addEventListener('submit', e => { e.preventDefault(); sendMessage(els.input.value); });
  els.input.addEventListener('input', resizeInput);
  els.input.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); els.composer.requestSubmit(); } });
  els.modal.addEventListener('click', e => { if (e.target === els.modal) closeSettings(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSettings(); });
  document.querySelectorAll('.prompt-card').forEach(button => button.addEventListener('click', () => { els.input.value = button.dataset.prompt || ''; resizeInput(); els.input.focus(); }));

  loadLocal(); render();
  api('status').then(result => { configured = result.configured; keyPresent = result.keyPresent; window.serverConfig = result.config; renderSidebar(); if (!configured || !keyPresent) els.callout.classList.add('visible'); }).catch(() => { els.callout.classList.add('visible'); });
})();
</script>
</body>
</html>
