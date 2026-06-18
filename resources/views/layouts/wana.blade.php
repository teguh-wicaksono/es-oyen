<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Wana Cafe' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #1b1009;
            --muted: #765f4d;
            --line: rgba(83, 58, 38, .14);
            --cream: #f5eadb;
            --paper: #fffaf2;
            --coffee: #2b160b;
            --caramel: #c8844f;
            --sage: #647a54;
            --berry: #a64f53;
            --gold: #e3b467;
            --shadow: 0 24px 70px rgba(49, 29, 15, .12);
            --topbar-height: 72px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--ink);
            font-family: Inter, system-ui, sans-serif;
            background: #f7f0e7;
            min-height: 100vh;
        }

        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font: inherit; }
        button { cursor: pointer; }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 30;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 16px;
            min-height: 72px;
            padding: 12px 24px 12px 28px;
            color: var(--ink);
            background: rgba(255, 250, 246, .96);
            backdrop-filter: blur(12px);
            box-shadow: 0 12px 34px rgba(49, 29, 15, .08);
            border: 1px solid rgba(215, 191, 162, .34);
            border-radius: 10px 10px 0 0;
        }

        .topbar-left,
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .topbar-actions {
            justify-content: flex-end;
        }

        .topbar-center {
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }

        .topbar-greeting {
            min-width: 0;
            display: grid;
            gap: 3px;
            padding: 0 8px;
        }

        .topbar-greeting strong {
            display: block;
            color: var(--coffee);
            font-size: 14px;
            font-weight: 900;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-greeting span {
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 0;
        }

        .topbar-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 34px;
            padding: 0 12px;
            border: 1px solid rgba(83, 58, 38, .1);
            border-radius: 999px;
            color: var(--coffee);
            background: rgba(255, 253, 249, .72);
            box-shadow: 0 12px 28px rgba(49, 29, 15, .05);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .topbar-chip i {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--sage);
            box-shadow: 0 0 0 4px rgba(100, 122, 84, .12);
        }

        .topbar-chip:nth-child(2) i {
            background: var(--caramel);
            box-shadow: 0 0 0 4px rgba(200, 132, 79, .13);
        }

        .topbar-chip:nth-child(3) i {
            background: var(--berry);
            box-shadow: 0 0 0 4px rgba(166, 79, 83, .13);
        }

        .brand {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            padding: 7px 14px 7px 7px;
            border: 1px solid rgba(83, 58, 38, .10);
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(255, 253, 249, .92), rgba(246, 231, 211, .78));
            box-shadow: 0 12px 30px rgba(49, 29, 15, .06);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .brand:hover {
            transform: translateY(-1px);
            border-color: rgba(200, 132, 79, .22);
            box-shadow: 0 18px 38px rgba(49, 29, 15, .10);
        }

        .brand-mark {
            position: relative;
            display: grid;
            place-items: center;
            width: 50px;
            height: 50px;
            border-radius: 16px;
            color: #fff8ed;
            background:
                radial-gradient(circle at 32% 24%, rgba(255, 232, 184, .72), transparent 27%),
                linear-gradient(145deg, #261209 0%, #6f4329 58%, #b67845 100%);
            font-size: 0;
            flex: 0 0 auto;
            box-shadow: 0 16px 32px rgba(43, 22, 11, .20), inset 0 0 0 1px rgba(255, 248, 237, .20);
        }

        .brand-mark::after {
            content: "";
            position: absolute;
            right: -3px;
            bottom: -3px;
            width: 13px;
            height: 13px;
            border: 3px solid #fffaf6;
            border-radius: 50%;
            background: var(--sage);
        }

        .brand-mark svg {
            width: 27px;
            height: 27px;
            stroke: currentColor;
            stroke-width: 2.2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .brand-copy {
            display: block;
            min-width: 0;
            padding-right: 2px;
        }

        .brand strong {
            display: block;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 23px;
            line-height: 1;
            color: var(--coffee);
            white-space: nowrap;
        }

        .brand-copy span {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            min-height: 20px;
            margin-top: 5px;
            padding: 0 9px;
            border-radius: 999px;
            color: #6f4c34;
            background: rgba(227, 180, 103, .22);
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0;
            text-transform: none;
        }

        .sidebar-toggle {
            display: inline-grid;
            place-items: center;
            width: 46px;
            height: 46px;
            padding: 0;
            border: 1px solid rgba(83, 58, 38, .14);
            border-radius: 16px;
            color: #fff8ed;
            background: var(--coffee);
            box-shadow: 0 16px 34px rgba(49, 29, 15, .16);
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .sidebar-toggle:hover {
            transform: translateY(-1px);
            box-shadow: 0 20px 42px rgba(49, 29, 15, .2);
        }

        .sidebar-toggle svg {
            width: 22px;
            height: 22px;
            stroke: currentColor;
            stroke-width: 2.2;
            fill: none;
        }

        .toggle-icon-closed {
            display: none;
        }

        body.sidebar-collapsed .toggle-icon-open {
            display: none;
        }

        body.sidebar-collapsed .toggle-icon-closed {
            display: block;
        }

        .nav-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 0 14px;
            border: 0;
            border-radius: 999px;
            color: #211006;
            background: #f7d9ac;
            font-size: 13px;
            font-weight: 900;
        }

        .sidebar {
            position: fixed;
            top: var(--topbar-height);
            right: 0;
            z-index: 25;
            display: flex;
            flex-direction: column;
            width: 288px;
            height: calc(100vh - var(--topbar-height));
            padding: 18px 16px 20px;
            border-left: 1px solid rgba(83, 58, 38, .12);
            background: rgba(255, 250, 246, .97);
            backdrop-filter: blur(12px);
            box-shadow: -18px 0 44px rgba(49, 29, 15, .08);
            overflow: auto;
            transform: translateX(0);
            transition: transform .22s ease;
        }

        .sidebar-inner {
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-height: 100%;
        }

        .sidebar-section {
            display: grid;
            gap: 12px;
        }

        .sidebar-brand {
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr);
            align-items: center;
            gap: 18px;
            padding: 14px 16px 14px 12px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff8ef;
        }

        .sidebar-brand .brand-mark {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            justify-self: start;
        }

        .sidebar-brand .brand-mark svg {
            width: 32px;
            height: 32px;
            stroke-width: 3;
        }

        .sidebar-brand .brand-mark::after {
            right: -5px;
            bottom: 2px;
            width: 14px;
            height: 14px;
            border-width: 3px;
        }

        .sidebar-brand .brand-copy {
            min-width: 0;
            padding: 0;
        }

        .sidebar-brand .brand-copy strong {
            display: block;
            color: var(--coffee);
            font-family: Inter, system-ui, sans-serif;
            font-size: 18px;
            font-weight: 900;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-brand .brand-copy span {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            max-width: 100%;
            min-height: 24px;
            margin-top: 8px;
            padding: 0 12px;
            border-radius: 999px;
            color: #6f4c34;
            background: rgba(227, 180, 103, .22);
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
        }

        .nav {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start;
            gap: 8px;
            padding: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .nav a {
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            min-height: 42px;
            padding: 0 14px;
            border-radius: 14px;
            color: #6d625d;
            font-size: 14px;
            font-weight: 800;
            transition: all .2s ease;
        }

        .nav a svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            stroke-width: 2.2;
            fill: none;
            flex: 0 0 auto;
        }

        .nav a.active {
            color: #3a2b24;
            background: #ede8e5;
            box-shadow: inset 0 0 0 1px rgba(71, 51, 38, .04);
        }

        .sidebar-action-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 46px;
            padding: 0 14px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff6ea;
            color: var(--coffee);
            font-size: 14px;
            font-weight: 900;
        }

        .sidebar-action-link svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            stroke-width: 2.2;
            fill: none;
            flex: 0 0 auto;
        }

        .sidebar-footer {
            display: grid;
            gap: 12px;
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .sidebar-footer .user-menu {
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .sidebar-footer .user-meta {
            text-align: left;
        }

        .logout-form { margin: 0; }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
        }

        .user-meta {
            text-align: right;
        }

        .user-menu strong {
            display: block;
            color: #3a2b24;
            font-size: 12px;
            line-height: 1.1;
            font-weight: 900;
        }

        .user-menu span {
            display: block;
            margin-top: 2px;
            color: #9a8b83;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: none;
        }

        .avatar {
            position: relative;
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            border-radius: 15px;
            color: #fff8ed;
            background:
                radial-gradient(circle at 30% 22%, rgba(227, 180, 103, .45), transparent 36%),
                linear-gradient(135deg, #2b160b, #8b5737);
            font-size: 12px;
            font-weight: 900;
            box-shadow: 0 12px 24px rgba(49, 29, 15, .16), 0 0 0 3px #fff7f0;
            flex: 0 0 auto;
        }

        .avatar-owner {
            background:
                radial-gradient(circle at 28% 20%, rgba(245, 213, 146, .58), transparent 34%),
                linear-gradient(135deg, #2b160b, #8b5737);
        }

        .avatar-kasir {
            background:
                radial-gradient(circle at 28% 20%, rgba(255, 248, 237, .55), transparent 34%),
                linear-gradient(135deg, #4f6f52, #9f7043);
        }

        .avatar-dapur {
            background:
                radial-gradient(circle at 28% 20%, rgba(246, 215, 164, .5), transparent 34%),
                linear-gradient(135deg, #6d3728, #c8844f);
        }

        .avatar svg {
            width: 25px;
            height: 25px;
            stroke: currentColor;
            stroke-width: 3;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .avatar::after {
            content: "";
            position: absolute;
            right: -2px;
            bottom: -2px;
            width: 8px;
            height: 8px;
            border: 2px solid #fff7f0;
            border-radius: 50%;
            background: #58b66d;
        }

        .topbar-action {
            position: relative;
            display: inline-grid;
            place-items: center;
            width: 24px;
            height: 24px;
            border: 0;
            background: transparent;
            color: #5d514b;
            padding: 0;
        }

        .topbar-action svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            stroke-width: 2.2;
            fill: none;
        }

        .topbar-action.has-alert::after {
            content: "";
            position: absolute;
            top: 1px;
            right: 3px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #d02d2d;
            box-shadow: 0 0 0 2px #fffaf6;
        }

        .shell {
            width: auto;
            margin: 24px 312px 42px 24px;
            transform: translateX(0);
            transition: transform .24s ease;
        }

        body.sidebar-collapsed .sidebar {
            transform: translateX(100%);
        }

        body.sidebar-collapsed .shell {
            width: auto;
            margin: 24px 312px 42px 24px;
            transform: translateX(144px);
        }

        .page-head {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 18px;
            align-items: end;
            margin-bottom: 22px;
        }

        .eyebrow {
            color: var(--sage);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        h1, h2, h3, p { margin: 0; }

        h1 {
            margin-top: 6px;
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(34px, 4vw, 58px);
            line-height: .98;
        }

        .lead {
            max-width: 720px;
            margin-top: 12px;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .grid-main {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 380px;
            gap: 24px;
            align-items: start;
        }

        .grid-two {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .panel, .product-card, .metric, .table-wrap {
            border: 1px solid #ece3da;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 16px 38px rgba(52, 34, 18, .05);
        }

        .panel {
            padding: 20px;
        }

        .profile-shell {
            display: flex;
            justify-content: center;
            margin: 0 auto;
            width: 100%;
        }

        .profile-card {
            width: min(700px, 100%);
            border-radius: 24px;
            padding: 32px 30px;
            background: linear-gradient(180deg, rgba(255,255,255,.98) 0%, rgba(250,244,236,1) 100%);
            border: 1px solid rgba(216, 179, 141, .28);
            box-shadow: 0 32px 70px rgba(54, 32, 14, .12);
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-top: 22px;
            flex-wrap: wrap;
        }

        .panel-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .panel-title h2, .panel-title h3 {
            font-size: 18px;
            font-weight: 800;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .product-card {
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(49, 29, 15, .08);
        }

        .product-media {
            position: relative;
            aspect-ratio: 4 / 2.6;
            background: #d8c1a6;
            overflow: hidden;
        }

        .product-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 25px;
            padding: 5px 9px;
            border-radius: 999px;
            color: #fff;
            background: var(--sage);
            font-size: 11px;
            font-weight: 800;
        }

        .product-media .badge:first-child {
            position: absolute;
            top: 12px;
            left: 12px;
        }

        .stock-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(28, 20, 12, .72);
            backdrop-filter: blur(10px);
        }

        .product-body {
            padding: 16px;
        }

        .product-body h3 {
            font-family: "Playfair Display", Georgia, serif;
            font-size: 21px;
        }

        .product-body p {
            min-height: 42px;
            margin-top: 6px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.55;
        }

        .product-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 15px;
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .price {
            font-weight: 900;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 48px;
            padding: 0 18px;
            border: 0;
            border-radius: 14px;
            color: #fff8ed;
            background: var(--coffee);
            font-weight: 800;
            font-size: 15px;
            letter-spacing: .01em;
            transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
            box-shadow: 0 18px 40px rgba(43, 22, 11, .12);
        }

        .btn:hover {
            transform: translateY(-1px);
            background: #432414;
            box-shadow: 0 22px 48px rgba(43, 22, 11, .16);
        }

        .btn.secondary {
            color: var(--coffee);
            background: #f8e1b9;
            box-shadow: inset 0 0 0 1px rgba(43, 22, 11, .08);
        }

        .btn.ghost { color: var(--coffee); background: #f7efe3; border: 1px solid var(--line); }
        .btn.success { background: var(--sage); }
        .btn.warn { background: var(--berry); }

        .field {
            display: grid;
            gap: 10px;
            margin-bottom: 16px;
        }

        .field label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .field input, .field select, .field textarea {
            width: 100%;
            border: 1px solid rgba(93, 66, 44, .18);
            border-radius: 8px;
            background: #fffdf9;
            padding: 12px 13px;
            color: var(--ink);
            outline: none;
        }

        .field textarea { min-height: 92px; resize: vertical; }

        .cart-list, .order-list, .chat-list {
            display: grid;
            gap: 12px;
        }

        .mini-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 12px;
            padding: 14px;
            border: 1px solid #ece3da;
            border-radius: 12px;
            background: #fff;
        }

        .mini-item strong { display: block; font-size: 14px; }
        .mini-item span { color: var(--muted); font-size: 12px; }

        .qty {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qty button {
            width: 30px;
            height: 30px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #f8ecdc;
            font-weight: 900;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
            font-size: 18px;
            font-weight: 900;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .metric {
            padding: 18px;
        }

        .metric span {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .metric strong {
            display: block;
            margin-top: 8px;
            font-size: 28px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 13px 14px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            font-size: 14px;
        }

        th {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .table-wrap { overflow: hidden; }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .pill {
            padding: 10px 14px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(255, 250, 242, .85);
            color: var(--muted);
            font-weight: 800;
        }

        .empty {
            padding: 16px;
            border: 1px dashed rgba(140, 118, 98, .18);
            border-radius: 10px;
            color: var(--muted);
            background: rgba(247, 239, 230, .68);
            text-align: center;
        }

        .toast {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 50;
            display: none;
            max-width: 360px;
            padding: 14px 16px;
            border-radius: 10px;
            color: #fff8ed;
            background: var(--coffee);
            box-shadow: 0 20px 45px rgba(49, 29, 15, .14);
            font-weight: 800;
        }

        .toast.show { display: block; animation: rise .22s ease; }

        .live-popup {
            position: fixed;
            right: 20px;
            top: 92px;
            z-index: 100001;
            display: grid;
            gap: 12px;
            width: min(390px, calc(100vw - 32px));
            padding: 18px;
            border: 1px solid rgba(255, 250, 242, .62);
            border-radius: 22px;
            color: #fff8ed;
            background:
                radial-gradient(circle at 16% 0%, rgba(227, 180, 103, .24), transparent 34%),
                linear-gradient(135deg, rgba(43, 22, 11, .96), rgba(111, 67, 41, .94));
            box-shadow: 0 28px 80px rgba(24, 12, 6, .28);
            opacity: 0;
            transform: translateY(-10px) scale(.98);
            visibility: hidden;
            pointer-events: none;
            transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
        }

        .live-popup.show {
            opacity: 1;
            transform: translateY(0) scale(1);
            visibility: visible;
            pointer-events: auto;
        }

        .live-popup[data-type="chat"] {
            background:
                radial-gradient(circle at 16% 0%, rgba(100, 122, 84, .24), transparent 34%),
                linear-gradient(135deg, rgba(43, 22, 11, .96), rgba(78, 89, 57, .94));
        }

        .live-popup-head {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 12px;
            align-items: center;
        }

        .live-popup-icon {
            display: grid;
            place-items: center;
            width: 48px;
            height: 48px;
            border-radius: 16px;
            color: var(--coffee);
            background: #fff4df;
            box-shadow: inset 0 0 0 1px rgba(43, 22, 11, .08);
        }

        .live-popup-icon svg,
        .live-popup-close svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .live-popup-label {
            display: block;
            color: rgba(255, 248, 237, .72);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .live-popup-title {
            display: block;
            margin-top: 4px;
            font-size: 17px;
            font-weight: 900;
            line-height: 1.25;
        }

        .live-popup-close {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 12px;
            color: rgba(255, 248, 237, .78);
            background: rgba(255, 250, 242, .08);
        }

        .live-popup-close:hover {
            color: #fff8ed;
            background: rgba(255, 250, 242, .14);
        }

        .live-popup-body {
            color: rgba(255, 248, 237, .86);
            font-size: 13px;
            font-weight: 700;
            line-height: 1.6;
        }

        .live-popup-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .live-popup-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            color: var(--coffee);
            background: #fff4df;
            font-size: 13px;
            font-weight: 900;
        }

        .live-popup[data-type="chat"] .live-popup-link {
            background: #f7d9ac;
            box-shadow: 0 12px 28px rgba(20, 10, 5, .18);
        }

        .live-popup-reply {
            display: grid;
            gap: 10px;
            padding-top: 2px;
        }

        .live-popup-reply[hidden] {
            display: none;
        }

        .live-popup-reply textarea {
            width: 100%;
            min-height: 82px;
            resize: vertical;
            border: 1px solid rgba(255, 250, 242, .24);
            border-radius: 16px;
            padding: 12px 13px;
            color: #fff8ed;
            background: rgba(255, 250, 242, .1);
            outline: 0;
            font-size: 13px;
            font-weight: 750;
        }

        .live-popup-reply textarea::placeholder {
            color: rgba(255, 248, 237, .58);
        }

        .live-popup-send {
            justify-self: end;
            min-height: 40px;
            padding: 0 16px;
            border: 0;
            border-radius: 999px;
            color: var(--coffee);
            background: #f7d9ac;
            font-size: 13px;
            font-weight: 900;
            box-shadow: 0 12px 28px rgba(20, 10, 5, .18);
        }

        .page-loader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f3eb;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity .16s ease, visibility .16s ease;
        }

        .page-loader-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            transform: translateY(-2vh);
        }

        .page-loader.show {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .page-loader .coffee-image {
            width: clamp(390px, 28vw, 520px);
            height: auto;
            max-height: 42vh;
            object-fit: contain;
            animation: loaderBounce 1.5s infinite;
        }

        .page-loader h1 {
            margin: -46px 0 0;
            color: #8b4513;
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(36px, 4vw, 56px);
            font-weight: 800;
            line-height: 1;
        }

        .page-loader p {
            margin: 0;
            color: #9a4f17;
            font-size: 18px;
        }

        .page-loader .loading-bar {
            width: min(250px, 60vw);
            height: 6px;
            margin-top: 14px;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(139, 69, 19, .2);
        }

        .page-loader .loading-progress {
            width: 45%;
            height: 100%;
            border-radius: 999px;
            background: #b8860b;
            animation: loaderProgress 1.1s ease-in-out infinite;
        }

        .page-loader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: grid;
            place-items: center;
            padding: 24px;
            background:
                radial-gradient(circle at 48% 38%, rgba(227, 180, 103, .18), transparent 34%),
                rgba(255, 250, 244, .94);
            backdrop-filter: blur(14px);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity .2s ease, visibility .2s ease;
        }

        .page-loader.show {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .loader-card {
            display: grid;
            justify-items: center;
            gap: 16px;
            min-width: min(300px, 86vw);
            padding: 28px 26px;
            border: 1px solid rgba(83, 58, 38, .12);
            border-radius: 24px;
            background: rgba(255, 253, 249, .92);
            box-shadow: 0 30px 90px rgba(49, 29, 15, .18);
        }

        .loader-logo {
            position: relative;
            display: grid;
            place-items: center;
            width: 74px;
            height: 74px;
            border-radius: 24px;
            color: #fff8ed;
            background:
                radial-gradient(circle at 32% 24%, rgba(255, 232, 184, .72), transparent 28%),
                linear-gradient(145deg, #261209 0%, #6f4329 58%, #b67845 100%);
            box-shadow: 0 18px 42px rgba(43, 22, 11, .22);
        }

        .loader-logo svg {
            width: 42px;
            height: 42px;
            stroke: currentColor;
            stroke-width: 3.3;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .loader-ring {
            position: absolute;
            inset: -7px;
            border: 3px solid rgba(200, 132, 79, .18);
            border-top-color: var(--coffee);
            border-radius: 28px;
            animation: loaderSpin .82s linear infinite;
        }

        .loader-card strong {
            color: var(--coffee);
            font-family: "Playfair Display", Georgia, serif;
            font-size: 25px;
            line-height: 1;
        }

        .loader-card span {
            color: var(--muted);
            font-size: 13px;
            font-weight: 800;
        }

        .auth-wrap {
            display: grid;
            min-height: calc(100vh - 122px);
            place-items: center;
        }

        .auth-card {
            width: min(440px, 100%);
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 250, 242, .94);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .auth-cover {
            min-height: 150px;
            padding: 24px;
            color: #fff8ed;
            background:
                linear-gradient(rgba(34, 17, 8, .28), rgba(34, 17, 8, .68)),
                url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1200&q=80') center/cover;
        }

        .auth-cover h1 {
            max-width: 320px;
            color: #fff8ed;
            font-size: 42px;
        }

        .auth-body { padding: 24px; }

        .alert {
            margin-bottom: 14px;
            padding: 12px 14px;
            border-radius: 8px;
            color: #fff8ed;
            background: var(--berry);
            font-size: 13px;
            font-weight: 800;
        }

        .alert.success { background: var(--sage); }

        @keyframes rise {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes loaderSpin {
            to { transform: rotate(360deg); }
        }

        @keyframes loaderBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-30px); }
        }

        @keyframes loaderProgress {
            0% { transform: translateX(-120%); }
            100% { transform: translateX(240%); }
        }

        @media (max-width: 1100px) {
            .grid-main, .grid-two { grid-template-columns: 1fr; }
            .menu-grid, .metric-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }

            .topbar {
                grid-template-columns: minmax(0, 1fr) auto;
            }

            .topbar-center {
                order: 3;
                grid-column: 1 / -1;
                justify-content: flex-start;
                overflow-x: auto;
                padding: 2px 0 4px;
            }

            .topbar-center::-webkit-scrollbar {
                display: none;
            }
        }

        @media (max-width: 980px) {
            .topbar {
                padding: 12px 16px;
                border-radius: 0;
            }

            .sidebar {
                width: min(300px, 86vw);
                transform: translateX(102%);
            }

            .shell {
                width: auto;
                margin: 20px 12px 32px;
            }

            body.sidebar-collapsed .shell {
                width: auto;
                margin: 20px 12px 32px;
            }

            .sidebar-overlay {
                position: fixed;
                inset: var(--topbar-height) 0 0 0;
                z-index: 24;
                background: rgba(24, 16, 10, .34);
                opacity: 0;
                pointer-events: none;
                display: block;
                transition: opacity .22s ease;
            }

            body.sidebar-collapsed .sidebar-overlay {
                opacity: 0;
                pointer-events: none;
            }

            body:not(.sidebar-collapsed) .sidebar {
                transform: translateX(0);
            }

            body:not(.sidebar-collapsed) .sidebar-overlay {
                opacity: 1;
                pointer-events: auto;
            }
        }

        @media (max-width: 760px) {
            .topbar {
                padding: 12px 14px;
            }

            .brand strong {
                font-size: 18px;
            }

            .sidebar-toggle {
                width: 44px;
                height: 44px;
                border-radius: 12px;
            }

            .page-head {
                grid-template-columns: 1fr;
            }

            .menu-grid, .metric-grid {
                grid-template-columns: 1fr;
            }

            .shell {
                margin-top: 18px;
            }

            .auth-wrap {
                min-height: calc(100vh - 96px);
            }
        }

        .auth-page .topbar { display: none; }
        .auth-page .sidebar,
        .auth-page .sidebar-overlay { display: none !important; }
        .auth-page .shell { width: 100% !important; margin: 0 !important; padding: 0; }
        body.auth-page { min-height: 100vh; }
    </style>
    @stack('styles')
    <style>
        /* Responsive polish for partner devices: phone first, tablet comfortable, desktop spacious. */
        html {
            -webkit-text-size-adjust: 100%;
        }

        body {
            overflow-x: hidden;
        }

        img,
        svg,
        video {
            max-width: 100%;
        }

        .table-wrap,
        .panel,
        .product-card,
        .metric,
        .dashboard-card,
        .chat-board,
        .compose-panel,
        .cart-panel,
        .profile-card {
            min-width: 0;
        }

        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (min-width: 981px) and (max-width: 1280px) {
            .shell {
                margin: 22px 304px 36px 18px;
            }

            body.sidebar-collapsed .shell {
                width: auto;
                margin: 22px 304px 36px 18px;
                transform: translateX(143px);
            }

            .menu-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .metric-grid,
            .dashboard-cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 980px) {
            .topbar {
                grid-template-columns: minmax(0, 1fr) auto;
            }

            .brand-copy span {
                max-width: 46vw;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .shell,
            body.sidebar-collapsed .shell {
                width: auto;
                max-width: none;
                margin: 18px 14px 32px;
                transform: none;
            }

            .sidebar {
                top: var(--topbar-height);
                height: calc(100dvh - var(--topbar-height));
            }

            .grid-main,
            .grid-two,
            .page-head,
            .dashboard-grid,
            .order-layout,
            .chat-layout,
            .profile-layout {
                grid-template-columns: 1fr !important;
            }

            .menu-toolbar,
            .sidebar-panel,
            .chat-panel,
            .compose-panel,
            .profile-side {
                position: static !important;
            }
        }

        @media (max-width: 760px) {
            .topbar {
                min-height: 64px;
                gap: 10px;
                padding: 10px 12px;
            }

            .topbar-greeting {
                display: none;
            }

            .topbar-status {
                justify-content: flex-start;
                overflow-x: auto;
            }

            .topbar-status::-webkit-scrollbar {
                display: none;
            }

            .brand {
                gap: 9px;
                padding: 6px 10px 6px 6px;
                border-radius: 16px;
            }

            .brand-mark {
                width: 42px;
                height: 42px;
                border-radius: 14px;
            }

            .brand-mark svg {
                width: 23px;
                height: 23px;
            }

            .sidebar-brand .brand-mark {
                width: 58px;
                height: 58px;
                border-radius: 18px;
            }

            .sidebar-brand .brand-mark svg {
                width: 32px;
                height: 32px;
            }

            .brand strong {
                font-size: 17px;
            }

            .brand-copy span {
                max-width: 42vw;
                font-size: 9px;
            }

            .sidebar {
                top: var(--topbar-height);
                width: min(320px, 88vw);
                height: calc(100dvh - var(--topbar-height));
            }

            .sidebar-overlay {
                inset: var(--topbar-height) 0 0 0;
            }

            .sidebar-toggle {
                width: 42px;
                height: 42px;
                border-radius: 14px;
            }

            .shell,
            body.sidebar-collapsed .shell {
                margin: 14px 10px 28px;
                transform: none;
            }

            h1,
            .order-hero h1,
            .chat-hero h1,
            .kasir-hero h1,
            .hero-copy h1,
            .profile-hero h1,
            .history-hero h1,
            .kitchen-hero h1 {
                font-size: clamp(30px, 10vw, 42px) !important;
                line-height: 1.04;
            }

            .lead,
            .order-hero .lead,
            .chat-hero .lead,
            .hero-copy .lead {
                font-size: 14px !important;
                line-height: 1.58;
            }

            .order-hero,
            .chat-hero,
            .kasir-hero,
            .profile-hero,
            .history-hero,
            .kitchen-hero {
                grid-template-columns: 1fr !important;
                min-height: auto !important;
                gap: 18px !important;
                margin-bottom: 16px !important;
                padding: 18px !important;
                border-radius: 20px !important;
            }

            .order-hero::before,
            .chat-hero::before,
            .kasir-hero::before,
            .profile-hero::before,
            .history-hero::before,
            .kitchen-hero::before {
                inset: 9px !important;
                border-radius: 16px !important;
            }

            .order-hero-visual,
            .chat-hero-visual,
            .hero-visual,
            .profile-hero-visual,
            .history-hero-visual {
                min-height: 190px !important;
            }

            .order-hero-visual img,
            .chat-hero-visual img,
            .profile-hero-visual img,
            .history-hero-visual img,
            .coffee-orbit {
                width: min(240px, 72vw) !important;
            }

            .hero-stats,
            .chat-hero-pills,
            .hero-actions,
            .filter-row,
            .chat-filter,
            .toolbar {
                display: flex;
                flex-wrap: nowrap;
                gap: 8px;
                overflow-x: auto;
                padding-bottom: 2px;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .hero-stats::-webkit-scrollbar,
            .chat-hero-pills::-webkit-scrollbar,
            .hero-actions::-webkit-scrollbar,
            .filter-row::-webkit-scrollbar,
            .chat-filter::-webkit-scrollbar,
            .toolbar::-webkit-scrollbar {
                display: none;
            }

            .hero-stats span,
            .chat-hero-pills span,
            .filter-row .pill,
            .chat-chip,
            .toolbar .pill {
                flex: 0 0 auto;
            }

            .btn,
            .nav-button,
            .pill,
            .chat-chip,
            .target-button,
            .qty button {
                min-height: 44px;
            }

            .menu-grid,
            .metric-grid,
            .dashboard-cards,
            .dashboard-grid,
            .stock-grid,
            .product-grid,
            .summary-grid,
            .profile-stats,
            .field-grid,
            .target-buttons {
                grid-template-columns: 1fr !important;
            }

            .panel,
            .kasir-panel,
            .chat-board,
            .compose-panel,
            .cart-panel,
            .product-card,
            .dashboard-card,
            .metric,
            .profile-card {
                border-radius: 18px !important;
            }

            .panel,
            .kasir-panel,
            .chat-board,
            .compose-body,
            .profile-card {
                padding: 16px !important;
            }

            .product-media,
            .product-media img {
                min-height: 170px !important;
                height: 170px !important;
            }

            .product-headline,
            .product-foot,
            .panel-title,
            .chat-board-head,
            .mini-item,
            .order-list .mini-item {
                align-items: stretch;
                grid-template-columns: 1fr !important;
            }

            .product-headline,
            .product-foot,
            .panel-title,
            .chat-board-head {
                flex-direction: column;
            }

            .price,
            .chat-time {
                white-space: normal;
            }

            .cart-list {
                max-height: none !important;
                overflow: visible !important;
            }

            .toast {
                right: 10px;
                bottom: 10px;
                left: 10px;
                max-width: none;
            }

            .live-popup {
                top: 82px;
                right: 10px;
                left: 10px;
                width: auto;
            }
        }

        @media (max-width: 480px) {
            .topbar-left {
                gap: 8px;
            }

            .brand-copy span {
                display: none;
            }

            .brand strong {
                font-size: 16px;
            }

            .shell,
            body.sidebar-collapsed .shell {
                margin: 12px 8px 24px;
                transform: none;
            }

            .auth-body {
                padding: 18px;
            }

            .auth-cover h1 {
                font-size: 34px;
            }

            .dashboard-card,
            .metric {
                min-height: auto;
                padding: 16px;
            }

            th,
            td {
                padding: 11px 12px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body class="{{ request()->routeIs('login') ? 'auth-page' : '' }}">
    @php
        $icons = [
            'home' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10.5V20h13v-9.5"/><path d="M9.5 20v-5h5v5"/></svg>',
            'utensils' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3v8"/><path d="M10 3v8"/><path d="M4 7h8"/><path d="M8 11v10"/><path d="m16 3 4 4"/><path d="m14 9 7-7"/><path d="m15 8 5 5"/><path d="m13 21 5-5"/></svg>',
            'receipt' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-2-1.2-2 1.2-2-1.2-2 1.2-2-1.2L6 21V3Z"/><path d="M9 8h6"/><path d="M9 12h6"/><path d="M9 16h4"/></svg>',
            'brand' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M18 24h25v12a12 12 0 0 1-12 12h-1a12 12 0 0 1-12-12V24Z"/><path d="M43 29h4a7 7 0 0 1 0 14h-5"/><path d="M24 18v-4"/><path d="M32 18v-4"/><path d="M40 18v-4"/><path d="M20 51h28"/><path d="M25 31h10"/><path d="M25 37h7"/></svg>',
            'avatarOwner' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 4h10v5a5 5 0 0 1-10 0V4Z"/><path d="M7 7H4a3 3 0 0 0 3 3"/><path d="M17 7h3a3 3 0 0 1-3 3"/></svg>',
            'avatarKasir' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 7h14v12H5z"/><path d="M8 7V5h8v2"/><path d="M8 12h3"/><path d="M15 12h1"/><path d="M8 16h8"/></svg>',
            'avatarDapur' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3v8"/><path d="M10 3v8"/><path d="M4 7h8"/><path d="M8 11v10"/><path d="M16 4c3 2 4 4 4 7a6 6 0 0 1-6 6h-1"/><path d="M15 21h6"/></svg>',
            'chart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19h16"/><path d="M7 16V9"/><path d="M12 16V5"/><path d="M17 16v-4"/></svg>',
            'bell' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>',
            'chat' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5h14v11H8l-3 3V5Z"/></svg>',
            'sidebarOpen' => '<svg class="toggle-icon-open" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="3"/><path d="M14 5v14"/><path d="m10 9-3 3 3 3"/></svg>',
            'sidebarClosed' => '<svg class="toggle-icon-closed" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="3"/><path d="M10 5v14"/><path d="m14 9 3 3-3 3"/></svg>',
        ];
    @endphp

    @auth
        @php
            $role = auth()->user()->role;
            $roleLabel = ucfirst($role);
            $hour = now()->timezone(config('app.timezone'))->hour;
            $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
            $todayLabel = now()->timezone(config('app.timezone'))->isoFormat('dddd, D MMMM YYYY');
            $avatarIcons = [
                'owner' => 'avatarOwner',
                'kasir' => 'avatarKasir',
                'dapur' => 'avatarDapur',
            ];
            $roleNav = [
                'kasir' => [
                    ['label' => 'Dashboard', 'route' => 'kasir', 'icon' => 'home'],
                    ['label' => 'Menu', 'route' => 'kasir.pesanan', 'icon' => 'utensils'],
                    ['label' => 'Riwayat', 'route' => 'kasir.riwayat', 'icon' => 'receipt'],
                ],
                'dapur' => [
                    ['label' => 'Home', 'route' => 'dapur', 'icon' => 'home'],
                    ['label' => 'Antrian', 'route' => 'dapur.status', 'icon' => 'chart'],
                    ['label' => 'Produk', 'route' => 'dapur.produk', 'icon' => 'utensils'],
                    ['label' => 'Stok', 'route' => 'dapur.stok', 'icon' => 'chart'],
                    ['label' => 'Riwayat', 'route' => 'dapur.riwayat', 'icon' => 'receipt'],
                ],
                'owner' => [
                    ['label' => 'Analytics', 'route' => 'owner', 'icon' => 'chart'],
                    ['label' => 'Penjualan', 'route' => 'owner.penjualan', 'icon' => 'receipt'],
                    ['label' => 'Stok Produk', 'route' => 'owner.stok', 'icon' => 'chart'],
                    ['label' => 'Stok Bahan', 'route' => 'owner.stok-bahan', 'icon' => 'utensils'],
                    ['label' => 'Karyawan', 'route' => 'owner.karyawan', 'icon' => 'home'],
                    ['label' => 'Export', 'route' => 'owner.export', 'icon' => 'receipt'],
                ],
            ][$role] ?? [];
        @endphp
    @endauth

    <header class="topbar">
        <div class="topbar-left">
            <a class="brand" href="{{ auth()->check() ? route(auth()->user()->role) : route('login') }}">
                <span class="brand-mark">{!! $icons['brand'] ?? '' !!}</span>
                <span class="brand-copy">
                    <strong>Wana Cafe</strong>
                    <span>{{ auth()->check() ? ucfirst(auth()->user()->role) . ' Dashboard' : 'Dashboard' }}</span>
                </span>
            </a>
        </div>

        @auth
            <div class="topbar-center" aria-label="Ringkasan operasional">
                <div class="topbar-greeting">
                    <strong>{{ $greeting }}, {{ auth()->user()->name }}</strong>
                    <span>{{ $todayLabel }} • {{ $roleLabel }} aktif</span>
                </div>
                <div class="topbar-status">
                    <span class="topbar-chip"><i aria-hidden="true"></i><span id="topbarOrderCount">0 order</span></span>
                    <span class="topbar-chip"><i aria-hidden="true"></i><span id="topbarChatCount">0 chat</span></span>
                    <span class="topbar-chip"><i aria-hidden="true"></i><span>Operasional</span></span>
                </div>
            </div>
        @endauth

        <div class="topbar-actions">
            @auth
                <button id="sidebarToggle" class="sidebar-toggle" type="button" aria-label="Buka atau tutup sidebar" aria-expanded="true">
                    {!! $icons['sidebarOpen'] !!}
                    {!! $icons['sidebarClosed'] !!}
                </button>
            @else
                <a class="nav-button" href="{{ route('login') }}">Sign In</a>
            @endauth
        </div>
    </header>

    @auth
        <aside id="roleSidebar" class="sidebar" aria-label="Navigasi role">
            <div class="sidebar-inner">
                <div class="sidebar-section sidebar-brand">
                    <span class="brand-mark avatar-{{ auth()->user()->role }}">{!! $icons[$avatarIcons[auth()->user()->role] ?? 'brand'] ?? '' !!}</span>
                    <span class="brand-copy">
                        <strong>Wana Cafe</strong>
                        <span>{{ ucfirst(auth()->user()->role) }} Dashboard</span>
                    </span>
                </div>

                <nav class="nav" aria-label="Navigasi role">
                    @foreach ($roleNav as $item)
                        <a class="{{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{ route($item['route']) }}">
                            {!! $icons[$item['icon']] ?? '' !!}
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <a class="sidebar-action-link" href="{{ route(auth()->user()->role . '.chat') }}">
                    {!! $icons['chat'] !!}
                    Daftar Chat
                </a>

                <div class="sidebar-footer">
                    <div class="user-menu">
                        <div class="user-meta">
                            <strong>{{ auth()->user()->name }}</strong>
                            <span>{{ ucfirst(auth()->user()->role) }}</span>
                        </div>
                        <a class="avatar avatar-{{ auth()->user()->role }}" href="{{ auth()->user()->role === 'kasir' ? route('kasir.profil') : (auth()->user()->role === 'dapur' ? route('dapur.profil') : route('owner.profil')) }}" aria-label="Profil {{ auth()->user()->name }}">
                            {!! $icons[$avatarIcons[auth()->user()->role] ?? 'brand'] ?? '' !!}
                        </a>
                    </div>

                    <form class="logout-form" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="nav-button" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </aside>
        <div id="sidebarOverlay" class="sidebar-overlay" aria-hidden="true"></div>
    @endauth

    <main class="shell">
        @yield('content')
    </main>

    <div id="pageLoader" class="page-loader" aria-live="polite" aria-hidden="true">
        <div class="page-loader-inner">
            <img src="/images/loading.jpg" alt="Coffee" class="coffee-image">
            <h1>Wana Cafe</h1>
            <p>Sip the Perfect Brew</p>
            <div class="loading-bar">
                <div class="loading-progress"></div>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>
    @auth
        <div id="livePopup" class="live-popup" aria-live="polite" aria-hidden="true">
            <div class="live-popup-head">
                <span id="livePopupIcon" class="live-popup-icon">{!! $icons['bell'] ?? '' !!}</span>
                <div>
                    <span id="livePopupLabel" class="live-popup-label">Notifikasi</span>
                    <strong id="livePopupTitle" class="live-popup-title">Aktivitas baru</strong>
                </div>
                <button class="live-popup-close" type="button" onclick="closeLivePopup()" aria-label="Tutup notifikasi">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12"></path><path d="M18 6 6 18"></path></svg>
                </button>
            </div>
            <div id="livePopupBody" class="live-popup-body"></div>
            <div class="live-popup-actions">
                <a id="livePopupLink" class="live-popup-link" href="#">Buka</a>
            </div>
            <form id="livePopupReply" class="live-popup-reply" hidden>
                <textarea id="livePopupReplyInput" rows="3" placeholder="Tulis balasan..."></textarea>
                <button class="live-popup-send" type="submit">Kirim</button>
            </form>
        </div>
    @endauth
    <script>
        (function syncTopbarHeight() {
            const topbar = document.querySelector('.topbar');
            if (!topbar) return;

            const update = () => {
                document.documentElement.style.setProperty('--topbar-height', `${Math.ceil(topbar.getBoundingClientRect().height)}px`);
            };

            update();
            window.addEventListener('load', update);
            window.addEventListener('resize', update);
            window.addEventListener('orientationchange', update);
        })();

        (function initPageLoader() {
            const loader = document.getElementById('pageLoader');
            if (!loader) return;

            let showTimer = null;

            const showLoader = () => {
                clearTimeout(showTimer);
                showTimer = setTimeout(() => {
                    loader.classList.add('show');
                    loader.setAttribute('aria-hidden', 'false');
                }, 90);
            };

            const hideLoader = () => {
                clearTimeout(showTimer);
                loader.classList.remove('show');
                loader.setAttribute('aria-hidden', 'true');
            };

            window.addEventListener('load', hideLoader);
            window.addEventListener('pageshow', hideLoader);
            window.addEventListener('beforeunload', showLoader);

            document.addEventListener('click', (event) => {
                const link = event.target.closest('a[href]');
                if (!link) return;

                const href = link.getAttribute('href');
                const target = link.getAttribute('target');

                if (
                    !href ||
                    href.startsWith('#') ||
                    href.startsWith('javascript:') ||
                    href.startsWith('mailto:') ||
                    href.startsWith('tel:') ||
                    target === '_blank' ||
                    link.hasAttribute('download') ||
                    event.defaultPrevented ||
                    event.metaKey ||
                    event.ctrlKey ||
                    event.shiftKey ||
                    event.altKey
                ) {
                    return;
                }

                showLoader();
            });

            document.addEventListener('submit', (event) => {
                if (!event.defaultPrevented) {
                    showLoader();
                }
            });
        })();

        (function initSidebarToggle() {
            const sidebar = document.getElementById('roleSidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (!sidebar || !sidebarToggle) return;

            const storageKey = 'wana_sidebar_collapsed';
            const mobileQuery = window.matchMedia('(max-width: 980px)');

            const getInitialCollapsed = () => {
                if (mobileQuery.matches) {
                    return true;
                }

                const stored = localStorage.getItem(storageKey);
                if (stored === null) {
                    return false;
                }

                return stored === '1';
            };

            const setCollapsed = (collapsed) => {
                document.body.classList.toggle('sidebar-collapsed', collapsed);
                sidebarToggle.setAttribute('aria-expanded', String(!collapsed));
                if (!mobileQuery.matches) {
                    localStorage.setItem(storageKey, collapsed ? '1' : '0');
                }
            };

            setCollapsed(getInitialCollapsed());

            const handleViewportChange = () => {
                if (mobileQuery.matches) {
                    setCollapsed(true);
                    return;
                }

                setCollapsed(localStorage.getItem(storageKey) === '1');
            };

            sidebarToggle.addEventListener('click', () => {
                setCollapsed(!document.body.classList.contains('sidebar-collapsed'));
            });

            sidebarOverlay?.addEventListener('click', () => {
                setCollapsed(true);
            });

            mobileQuery.addEventListener?.('change', handleViewportChange);
        })();

        const WANA_SEED_PRODUCTS = @json($products ?? []);
        const WANA_SEED_ORDERS = @json($orders ?? []);
        const WANA_SEED_CHATS = @json($chats ?? []);
        const WANA_SEED_MATERIALS = @json($materials ?? []);
        const WANA_SEED_ACTIVITIES = @json($activities ?? []);
        const WANA_CURRENT_ROLE = @json(auth()->check() ? ucfirst(auth()->user()->role) : null);
        const WANA_LIVE_FEED_URL = @json(auth()->check() ? route('live-notifications.feed') : null);
        const WANA_CHAT_READ_URL = @json(auth()->check() ? route('chat.read') : null);
        const WANA_IS_CHAT_PAGE = @json(request()->routeIs('kasir.chat', 'dapur.chat', 'owner.chat'));
        const WANA_ROLE_LINKS = @json(auth()->check() ? [
            'order' => auth()->user()->role === 'dapur' ? route('dapur.status') : (auth()->user()->role === 'owner' ? route('owner.penjualan') : route('kasir.riwayat')),
            'chat' => route(auth()->user()->role . '.chat'),
        ] : []);
        let wanaProductsCache = null;
        let wanaOrdersCache = null;
        let wanaChatsCache = null;
        let wanaMaterialsCache = null;
        let wanaActivitiesCache = null;

        const rupiah = (value) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(value);

        const store = {
            get(key, fallback) {
                const raw = localStorage.getItem(key);
                return raw ? JSON.parse(raw) : fallback;
            },
            set(key, value) {
                localStorage.setItem(key, JSON.stringify(value));
                window.dispatchEvent(new Event('wana:storage'));
            }
        };

        function getProducts() {
            if (wanaProductsCache) {
                return wanaProductsCache;
            }

            wanaProductsCache = WANA_SEED_PRODUCTS;
            store.set('wana_products', wanaProductsCache);
            return wanaProductsCache;
        }

        function setProducts(products) {
            wanaProductsCache = products;
            store.set('wana_products', products);
        }

        function getOrders() {
            if (wanaOrdersCache) {
                return wanaOrdersCache;
            }

            wanaOrdersCache = WANA_SEED_ORDERS;
            store.set('wana_orders', wanaOrdersCache);
            return wanaOrdersCache;
        }

        function setOrders(orders) {
            wanaOrdersCache = orders;
            store.set('wana_orders', orders);
            updateTopbarSummary();
        }

        function getComplaints() {
            if (wanaChatsCache) {
                return wanaChatsCache;
            }

            wanaChatsCache = WANA_SEED_CHATS;
            store.set('wana_complaints', wanaChatsCache);
            return wanaChatsCache;
        }

        function setComplaints(messages) {
            wanaChatsCache = messages;
            store.set('wana_complaints', messages);
            updateTopbarSummary();
        }

        function updateTopbarSummary() {
            const orderCount = document.getElementById('topbarOrderCount');
            const chatCount = document.getElementById('topbarChatCount');
            if (!orderCount && !chatCount) return;

            const orders = wanaOrdersCache ?? WANA_SEED_ORDERS;
            const chats = wanaChatsCache ?? WANA_SEED_CHATS;
            const activeOrders = orders.filter((order) => !['Selesai', 'Dibatalkan'].includes(order.status)).length;
            const visibleChats = WANA_CURRENT_ROLE
                ? chats.filter((chat) => chat.recipient === WANA_CURRENT_ROLE && chat.sender !== WANA_CURRENT_ROLE && !chat.read).length
                : chats.filter((chat) => !chat.read).length;

            if (orderCount) {
                orderCount.textContent = `${activeOrders} order aktif`;
            }

            if (chatCount) {
                chatCount.textContent = `${visibleChats} chat baru`;
            }
        }

        let wanaMarkingChatRead = false;

        async function markCurrentChatRead() {
            if (!WANA_CHAT_READ_URL || wanaMarkingChatRead) return;
            wanaMarkingChatRead = true;

            try {
                const payload = await wanaRequest(WANA_CHAT_READ_URL, { method: 'POST' });
                if (payload.chats) {
                    wanaChatsCache = payload.chats;
                    store.set('wana_complaints', payload.chats);
                    updateTopbarSummary();
                }
            } catch (error) {
                console.warn(error.message);
            } finally {
                wanaMarkingChatRead = false;
            }
        }

        window.addEventListener('storage', updateTopbarSummary);
        window.addEventListener('wana:storage', updateTopbarSummary);
        updateTopbarSummary();

        async function saveChatMessage(recipient, message) {
            const payload = await wanaRequest('/chat', {
                method: 'POST',
                body: JSON.stringify({ recipient, message })
            });
            const messages = [...getComplaints(), payload.chat];
            setComplaints(messages);
            return payload.chat;
        }

        function getKitchenHistory() {
            if (wanaActivitiesCache) {
                return wanaActivitiesCache;
            }

            wanaActivitiesCache = WANA_SEED_ACTIVITIES;
            store.set('wana_kitchen_history', wanaActivitiesCache);
            return wanaActivitiesCache;
        }

        function setKitchenHistory(items) {
            wanaActivitiesCache = items;
            store.set('wana_kitchen_history', items);
        }

        function getMaterials() {
            if (wanaMaterialsCache) {
                return wanaMaterialsCache;
            }

            wanaMaterialsCache = WANA_SEED_MATERIALS;
            store.set('wana_materials', wanaMaterialsCache);
            return wanaMaterialsCache;
        }

        function setMaterialsStore(items) {
            wanaMaterialsCache = items;
            store.set('wana_materials', items);
        }

        async function wanaRequest(url, options = {}) {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    ...(options.headers || {})
                },
                ...options
            });

            const payload = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(payload.message || 'Data gagal disimpan ke database.');
            }

            return payload;
        }

        async function logKitchenActivity(type, title, detail = '', meta = {}) {
            const entry = {
                id: `KH-${Date.now()}`,
                type,
                title,
                detail,
                meta,
                actor: @json(auth()->check() ? auth()->user()->name : 'Sistem'),
                time: new Date().toLocaleString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                })
            };

            setKitchenHistory([entry, ...getKitchenHistory()].slice(0, 120));
            try {
                const payload = await wanaRequest('/activity-log', {
                    method: 'POST',
                    body: JSON.stringify({ type, title, detail, meta })
                });
                setKitchenHistory([payload.activity, ...getKitchenHistory().filter((item) => item.id !== entry.id)].slice(0, 120));
            } catch (error) {
                console.warn(error.message);
            }
            return entry;
        }

        function notify(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            clearTimeout(window.wanaToastTimer);
            window.wanaToastTimer = setTimeout(() => toast.classList.remove('show'), 2600);
        }

        function openLivePopup({ type = 'order', label = 'Notifikasi', title = 'Aktivitas baru', body = '', link = '#', recipient = '' }) {
            const popup = document.getElementById('livePopup');
            if (!popup) return;

            const icons = {
                order: `{!! $icons['receipt'] ?? '' !!}`,
                chat: `{!! $icons['chat'] ?? '' !!}`,
                default: `{!! $icons['bell'] ?? '' !!}`
            };

            document.getElementById('livePopupIcon').innerHTML = icons[type] || icons.default;
            document.getElementById('livePopupLabel').textContent = label;
            document.getElementById('livePopupTitle').textContent = title;
            document.getElementById('livePopupBody').textContent = body;
            document.getElementById('livePopupLink').href = link;
            document.getElementById('livePopupLink').textContent = type === 'chat' ? 'Balas' : 'Lihat Pesanan';
            document.getElementById('livePopupReply')?.setAttribute('hidden', '');
            document.getElementById('livePopupReplyInput').value = '';

            popup.dataset.type = type;
            popup.dataset.recipient = recipient;
            popup.classList.add('show');
            popup.setAttribute('aria-hidden', 'false');
            clearTimeout(window.wanaLivePopupTimer);
            window.wanaLivePopupTimer = setTimeout(closeLivePopup, 6200);
        }

        function closeLivePopup() {
            const popup = document.getElementById('livePopup');
            if (!popup) return;

            popup.classList.remove('show');
            popup.setAttribute('aria-hidden', 'true');
            document.getElementById('livePopupReply')?.setAttribute('hidden', '');
        }

        function openLivePopupReply() {
            const popup = document.getElementById('livePopup');
            const replyForm = document.getElementById('livePopupReply');
            const replyInput = document.getElementById('livePopupReplyInput');
            if (!popup || popup.dataset.type !== 'chat' || !replyForm || !replyInput) return;

            clearTimeout(window.wanaLivePopupTimer);
            replyForm.removeAttribute('hidden');
            replyInput.focus();
        }

        async function sendLivePopupReply(event) {
            event.preventDefault();
            const popup = document.getElementById('livePopup');
            const input = document.getElementById('livePopupReplyInput');
            const recipient = popup?.dataset.recipient || '';
            const message = input?.value.trim() || '';

            if (!recipient) return notify('Penerima chat tidak ditemukan.');
            if (!message) return notify('Isi balasan dulu.');

            try {
                await saveChatMessage(recipient, message);
            } catch (error) {
                notify(error.message);
                return;
            }

            input.value = '';
            closeLivePopup();
            notify(`Balasan terkirim ke ${recipient}.`);
        }

        document.getElementById('livePopupLink')?.addEventListener('click', (event) => {
            const popup = document.getElementById('livePopup');
            if (popup?.dataset.type !== 'chat') return;

            event.preventDefault();
            openLivePopupReply();
        });

        document.getElementById('livePopupReply')?.addEventListener('submit', sendLivePopupReply);

        function newestByCreatedAt(items) {
            return [...(items || [])].sort((a, b) => {
                const left = Date.parse(String(a.createdAt || '').replace(/^(\d{2})\/(\d{2})\/(\d{4})/, '$3-$2-$1'));
                const right = Date.parse(String(b.createdAt || '').replace(/^(\d{2})\/(\d{2})\/(\d{4})/, '$3-$2-$1'));
                return (Number.isNaN(right) ? 0 : right) - (Number.isNaN(left) ? 0 : left);
            });
        }

        function initLiveNotifications() {
            if (!WANA_CURRENT_ROLE || !WANA_LIVE_FEED_URL) return;

            const roleKey = WANA_CURRENT_ROLE.toLowerCase();
            const orderSeenKey = `wana_live_seen_order_${roleKey}`;
            const chatSeenKey = `wana_live_seen_chat_${roleKey}`;
            let isRefreshing = false;

            const itemSummary = (order) => (order.items || [])
                .slice(0, 3)
                .map((item) => `${item.qty}x ${item.name}`)
                .join(', ');

            const showIncomingOrder = (orders) => {
                const latest = newestByCreatedAt(orders).find((order) => order.status === 'Masuk');
                if (!latest || latest.id === localStorage.getItem(orderSeenKey)) return false;

                localStorage.setItem(orderSeenKey, latest.id);
                openLivePopup({
                    type: 'order',
                    label: 'Pesanan Masuk',
                    title: `${latest.id} - ${latest.customer || 'Pelanggan'}`,
                    body: `${latest.table || 'Meja -'} | ${latest.cashier || 'Kasir'} | ${itemSummary(latest) || 'Detail pesanan tersedia.'}`,
                    link: WANA_ROLE_LINKS.order || '#'
                });
                notify(`Pesanan baru ${latest.id} masuk.`);
                return true;
            };

            const showIncomingChat = (chats) => {
                const latest = newestByCreatedAt(chats)
                    .filter((chat) => chat.recipient === WANA_CURRENT_ROLE && chat.sender !== WANA_CURRENT_ROLE && !chat.read)
                    [0];
                if (!latest || String(latest.id) === localStorage.getItem(chatSeenKey)) return false;

                localStorage.setItem(chatSeenKey, String(latest.id));
                openLivePopup({
                    type: 'chat',
                    label: 'Chat Masuk',
                    title: `${latest.sender} mengirim pesan`,
                    body: latest.message || 'Pesan baru masuk.',
                    link: WANA_ROLE_LINKS.chat || '#',
                    recipient: latest.sender
                });
                notify(`Chat baru dari ${latest.sender}.`);
                return true;
            };

            const refresh = async () => {
                if (isRefreshing) return;
                isRefreshing = true;

                try {
                    const payload = await wanaRequest(WANA_LIVE_FEED_URL, { method: 'GET' });
                    const orders = payload.orders || [];
                    const chats = payload.chats || [];

                    setOrders(orders);
                    setComplaints(chats);

                    if (WANA_IS_CHAT_PAGE) {
                        await markCurrentChatRead();
                        showIncomingOrder(orders);
                        return;
                    }

                    if (!showIncomingChat(chats)) {
                        showIncomingOrder(orders);
                    }
                } catch (error) {
                    console.warn(error.message);
                } finally {
                    isRefreshing = false;
                }
            };

            refresh();
            setInterval(refresh, 5000);
        }

        initLiveNotifications();

        (function initNumberInputPolish() {
            const cleanLeadingZero = (input) => {
                const value = input.value;
                if (!value) return;

                if (/^0+\./.test(value)) {
                    input.value = value.replace(/^0+\./, '0.');
                    return;
                }

                if (/^0+\d/.test(value)) {
                    input.value = value.replace(/^0+(\d)/, '$1');
                }
            };

            document.addEventListener('focusin', (event) => {
                const input = event.target;
                if (input instanceof HTMLInputElement && input.type === 'number' && input.value === '0') {
                    input.value = '';
                }
            });

            document.addEventListener('input', (event) => {
                const input = event.target;
                if (input instanceof HTMLInputElement && input.type === 'number') {
                    cleanLeadingZero(input);
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
