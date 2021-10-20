<?php
//樣板字典檔
$TplVar = Array(
"_Add"			=> "新增",
"_Edit"			=> "修改",
"_Copy"			=> "複製",
"_Del"			=> "刪除",
"_Save"			=> "存檔",
"_Cel"			=> "取消",
"_Close"		=> "關閉",
"_Sure"			=> "確定",
"_Set"			=> "設定",
"_All"			=> "全部",
"_Status"		=> "狀態",
"_Kind"			=> "類型",
"_Fun"			=> "功能",
"_Show"			=> "顯示",
"_SelectP"		=> "請選擇",
"_Search"		=> "搜尋",
"_Notes"		=> "備註",
"_Logout"		=> "登出",
"_No"			=> "編號",
"_Reset"		=> "重設",
"_View"			=> "查看",
"_Odr"			=> "排序",
"_Send"			=> "送出",
"_GoBack"		=> "返回",
"_Date"			=> "日期",

"_Del_Confirme"		=> "是否確定刪除?",
"_Not_Empty"		=> "必須輸入 ",

"_Status_A"		=> "全部",
"_Status_Y"		=> $LVar["Status_Y"],
"_Status_N"		=> $LVar["Status_N"],
"_Status_F"		=> $LVar["Status_F"],
"_Status_D"		=> $LVar["Status_D"],

"_Back"			=> "回上頁",
"_AllPage"		=> "總頁數",
"_Di"			=> "第",
"_Page"			=> "頁",
"_SelectAll"		=> "全選",
"_NoData"		=> "目前無任何資料",

//login
"_Username"		=> "帳號",
"_Passwd"		=> "密碼",
"_Login"		=> "登入",
"_IP"			=> "IP",

//body
"_Noframes"		=> "此網頁使用框架，但是您的瀏覽器不支援框架",

//customer
"_UserInfo"		=> "會員資料",
"_NewAg"		=> "新增代理",
"_NewMem"		=> "新增會員",
"_Nick"			=> "暱稱",
"_Upper"		=> "上層",
"_Currency"		=> "幣值",
"_Quota"		=> "額度",
"_AddDate"		=> "新增日期",
"_FuzzySearch"		=> "模糊搜尋",
"_DownCount"		=> "下線",

"_StatusN_Msg"		=> "注意：停用會將該層級以下所有的代理商、會員都停用！\\n\\n是否確定停用：  ",
"_StatusY_Msg"		=> "是否確定啟用： ",
"_StatusF_Msg"		=> "注意：凍結會使該層級以下所有的代理商、會員可進入系統但無法下注！\\n\\n是否確定凍結：  ",
"_StatusD_Msg"		=> "注意：刪除會將該層級以下所有的代理商、會員都刪除！\\n\\n刪除後資料不可復原！\\n\\n是否確定刪除：  ",
"_ForceLogout_Msg"	=> "注意：強制登出會將會員一同登出遊戲\\n\\n是否確定登出：  ",

//modify
"_LastEditDate"		=> "最後修改日期",
"_LastLoginIp"		=> "最後登入IP",

"_UserSet"		=> "帳號設定",
"_BasicSet"		=> "基本資料",
"_GameSet"		=> "遊戲設定",
"_ChkPwd"		=> "確認密碼",
"_UsernameNotes"	=> "(6~12碼英文數字組合)",
"_PwdNotes"		=> "(6~12碼英文數字組合)",
"_ChkPwdNotes"		=> "(請再次輸入密碼)",
"_Eor_Pwd2"		=> "確認密碼與密碼不符",
"_Eor_Pwd6"		=> "密碼必須為6~12碼英文數字組合，請重新輸入",
"_Rate"			=> "佔成",
"_Discount"		=> "退水",
"_FirstName"		=> "名",
"_LastName"		=> "姓",
"_RealName"		=> "真實姓名",
"_Email"		=> "E-Mail",
"_HomeTel"		=> "聯絡電話",
"_MobileTel"		=> "手機",
"_RegIp"		=> "註冊IP",

"_UserType"		=> "帳號類別",
"_UserType_C"		=> $LVar["UserType_C"],
"_UserType_T"		=> $LVar["UserType_T"],

"_isUnder"		=> "帳號類型",
"_isUnder_0"		=> "一般",
"_isUnder_1"		=> "直銷",

"_CanPlay"		=> "可否玩",
"_CanPlay_0"		=> "不可",
"_CanPlay_1"		=> "可",

"_*"			=> "<font color='#ff0000'><b>＊</b></font>",
"_*Notes"		=> "<font color='#ff0000'><b>＊</b></font> 為必填欄位",

//addpoint
"_Point"		=> "額度",
"_RestPoint"		=> "剩餘額度",
"_AddPoint_Note"	=> "請充值整數 (若要提出請填入負數)",

//admin
"_IsSub_1"		=> $LVar["IsSub_1"],	//子帳號
"_IsSub_0"		=> $LVar["IsSub_0"],	//一般
"_IsSubNote"		=> "子帳號 只有查詢權限, 無法做任何新增 修改 刪除動作",
"_Authority"		=> "權限",

//msg
"_Contents"		=> "內容",
"_EndDate"		=> "截止日期",

//notice
"_SetTop"		=> "置頂",
"_NoticeType"		=> "公告類別",

//online
"_LoginDate"		=> "登入時間",

//ctlip_set
"_DelIpConfirm"		=> "確定要刪除這個IP",

//self_info
"_Hd_UserInfo" 		=> $LVar["Hd_UserInfo"],

//record
"_Hd_RecordAdmin" 	=> $LVar["Hd_RecordAdmin"],
"_Hd_RecordCustomer" 	=> $LVar["Hd_RecordCustomer"],
"_ActName"		=> "操作人",
"_LType"		=> "類別",
"_LDesc"		=> "描述",

//recordflush
"_OpenQuota"		=> "開分額度",
"_OpenDT"		=> "開分日期",
"_OpenIP"		=> "開分IP",
"_ReturnQuota"		=> "洗分額度",
"_ReturnDT"		=> "洗分日期",
"_ReturnIP"		=> "洗分IP",

//recordpay
"_RealMoney"		=> "儲值金額",
"_Amount"		=> "儲值額度",
"_Bonus"		=> "贈送額度",
"_Total"		=> "總計",
"_PayType"		=> "儲值類別",
"_PayDT"		=> "儲值日期",

//recordshift
"_ShiftDT"		=> "轉移日期",

//recordquotatask
"_OldQuota"		=> "舊額度",
"_NewQuota"		=> "新額度",
"_QuotaMode"		=> "動作",

//open_game
"_OpenGame_1"		=> "開放中",
"_OpenGame_0"		=> "維護中",
"_ISTRIAL_0"		=> "正式",
"_ISTRIAL_1"		=> "測試",

//payout
"_PayoutRate"		=> "Payout比例",
"_Hardness"		=> "困難度係數",
"_CurRate"		=> "投注輸贏比例",
"_GPoint"		=> "總投注",
"_WPoint"		=> "總輸嬴",
"_Odds"			=> "賠率",
"_BetCnt"		=> "投注次數",

//report
"_BetUnit"		=> "交易單量",
"_BetGold"		=> "下注金額",
"_WinLose"		=> "會員輸贏",
"_RGold"		=> "上線輸贏",
"_LowRGold"		=> "下線輸贏",
"_FGold"		=> "上線退水",
"_LowFGold"		=> "下線退水",
"_WGold"		=> "上線交收",
"_LowWGold"		=> "下線交收",
"_Profit"		=> "淨利",
"_AllCount"		=> "總計",

"_LastLastWeek"		=> "上上週",
"_LastWeek"		=> "上週",
"_ThisWeek"		=> "本週",
"_BeforeWeek"		=> "前一週",
"_NextWeek"		=> "後一週",
"_BeforeDate"		=> "前一日",
"_NextDate"		=> "後一日",
"_Today"		=> "今日",
"_LastMonth"		=> "上月",
"_ThisMonth"		=> "本月",

//reportdetail
"_BetDate"		=> "下注時間",
"_Game"			=> "遊戲",
"_Result"		=> "結果",
"_PublicPoint"		=> "公點",

//report top 10
"_ReportTodayBet"	=> "當日遊戲報表",
"_ReportTopWin"		=> "前10名嬴玩家",
"_ReportTopLose"	=> "前10名輸玩家",
"_ReportTopBet"		=> "前10名高下注玩家",

"_Player"		=> "玩家統計",
"_WinPoint"		=> "贏額度",
"_LosePoint"		=> "輸額度",
"_WLPoint"		=> "輸贏額度",
"_TotalBet"		=> "總下注",
"_TotalGame"		=> "遊戲局數",

//reporttodaybet
"_ComProfitSum"		=> "營收統計",
"_PlayerProfitSum"	=> "玩家輸贏統計",
"_WinPlayer"		=> "贏統計",
"_LosePlayer"		=> "輸統計",
"_TiePlayer"		=> "平手統計",
);


//操作紀錄字典檔
$LogVar = Array(
"@status_y"		=> $TplVar["_Status_Y"],
"@status_n"		=> $TplVar["_Status_N"],
"@status_f"		=> $TplVar["_Status_F"],
"@status_d"		=> $TplVar["_Status_D"],
"@status"		=> $TplVar["_Status"],

"@addpoint"		=> $LVar["AddPoint"],
"@forcelogout"		=> $LVar["ForceLogout"],
"@issub=1"		=> $LVar["IsSub_1"],
"@issub=0"		=> $LVar["IsSub_0"],
"@type=Up"		=> "排序向上",
"@type=Down"		=> "排序向下",

"@result"		=> $TplVar["_Result"],
"@ldesc"		=> $TplVar["_LDesc"],
"@username"		=> $TplVar["_Username"],
"@nick"			=> $TplVar["_Nick"],
"@firstname"		=> $TplVar["_FirstName"],
"@lastname"		=> $TplVar["_LastName"],
"@realname"		=> $TplVar["_RealName"],
"@hometel"		=> $TplVar["_HomeTel"],
"@mobiletel"		=> $TplVar["_MobileTel"],
"@email"		=> $TplVar["_Email"],
"@discount"		=> $TplVar["_Discount"],
"@rate"			=> $TplVar["_Rate"],
"@usertype"		=> $TplVar["_UserType"],
"@hardness"		=> $TplVar["_Hardness"],

"@realmoney"		=> $TplVar["_RealMoney"],
"@adddate"		=> $TplVar["_AddDate"],
"@memmoney"		=> $TplVar["_MemMoney"],
"@authority"		=> $TplVar["_Authority"],
"@notes"		=> $TplVar["_Notes"],
"@note"			=> $TplVar["_Notes"],
"@paytype"		=> $TplVar["_PayType"],
"@subtype"		=> $TplVar["_PayType"]."2",
"@cash"			=> $TplVar["_RealMoney"],
"@amount"		=> $TplVar["_Amount"],
"@enddt"		=> $TplVar["_EndDate"],
"@enddate"		=> $TplVar["_EndDate"],
"@opengame=1"		=> $TplVar["_OpenGame_1"],
"@opengame=0"		=> $TplVar["_OpenGame_0"],
"@odds"			=> $TplVar["_Odds"],
"@pwdreset"		=> "重設密碼",
"@payrate"		=> "修改遊戲賠率",

"@userkind"		=> $TplVar["_isUnder_1"],
"@tableid"		=> "桌號",
"@gmid"			=> "GMid",
"@gid"			=> "Gid",
"@upid"			=> $TplVar["_Upper"],
"@cid"			=> $TplVar["_No"],
"@aid"			=> $TplVar["_No"],
"@pid"			=> $TplVar["_No"],
"@id"			=> $TplVar["_No"],

"@admin"		=> $LVar["Hd_Admin"],
"@msg_ag"		=> $LVar["Hd_MsgAg"],
"@msg_mem"		=> $LVar["Hd_MsgMem"],
"@open_game"		=> $LVar["Hd_GameOpen"],
"@payout"		=> $LVar["Hd_Payout"],
"@notice"		=> $LVar["Hd_Notice"],
"@ctlip"		=> $LVar["Hd_CtlIp"],
"@wwwip"		=> $LVar["Hd_WwwIp"],
"@customer"		=> $LVar["Hd_Customer"],
"@conf" 		=> $LVar["Hd_UserInfo"],
"@logout" 		=> $LVar["Hd_Logout"],

"@point"		=> $TplVar["_Point"],
"@odr"			=> $TplVar["_Odr"],
"@fee"			=> $TplVar["_Fee"],
"@login"		=> $TplVar["_Login"],
"@passwd"		=> $TplVar["_Passwd"],
"@pwd"			=> $TplVar["_Passwd"],
"@currency"		=> $TplVar["_Currency"],
"@add"			=> $TplVar["_Add"],
"@edit"			=> $TplVar["_Edit"],
"@del"			=> $TplVar["_Del"],
"@copy"			=> $TplVar["_Copy"],
"@msg"			=> $TplVar["_Contents"],
"@success"		=> "成功 ",
"@fail"			=> "失敗 ",

"Hd_ReportTodayBet" 	=> $LVar["Hd_ReportTodayBet"],
"Hd_ReportTopWin" 	=> $LVar["Hd_ReportTopWin"],
"Hd_ReportTopLose" 	=> $LVar["Hd_ReportTopLose"],
"Hd_ReportTopBet" 	=> $LVar["Hd_ReportTopBet"],

"Hd_WwwIp"		=> $LVar["Hd_WwwIp"],
"Hd_Online" 		=> $LVar["Hd_Online"],
"Hd_RecordLoginIp" 	=> $LVar["Hd_RecordLoginIp"],
"Hd_RecordPay"		=> $LVar["Hd_RecordPay"],
"Hd_RecordFlush"	=> $LVar["Hd_RecordFlush"],
"Hd_RecordShift"	=> $LVar["Hd_RecordShift"],
"Hd_RecordQuotaTask"	=> $LVar["Hd_RecordQuotaTask"],

"Hd_RecordSystem"	=> $LVar["Hd_RecordSystem"],
"Hd_RecordAdmin"	=> $LVar["Hd_RecordAdmin"],
"Hd_RecordCustomer"	=> $LVar["Hd_RecordCustomer"],

"Hd_MsgAg"		=> $LVar["Hd_MsgAg"],
"Hd_MsgMem"		=> $LVar["Hd_MsgMem"],
"Hd_Notice"		=> $LVar["Hd_Notice"],

"Hd_GameOpen"		=> $LVar["Hd_GameOpen"],
"Hd_Payout"		=> $LVar["Hd_Payout"],

"Hd_Admin"		=> $LVar["Hd_Admin"],
"Hd_CtlIp"		=> $LVar["Hd_CtlIp"],

"Hd_Report" 		=> $LVar["Hd_Report"],
"Hd_Customer"		=> $LVar["Hd_Customer"],
"Hd_Record"		=> $LVar["Hd_Record"],
"Hd_Other" 		=> $LVar["Hd_Other"],
"Hd_GameCtl"		=> $LVar["Hd_GameCtl"],
"Hd_SysAdmin" 		=> $LVar["Hd_SysAdmin"],
"Hd_UserInfo" 		=> $LVar["Hd_UserInfo"],
"Hd_Logout" 		=> $LVar["Hd_Logout"],
);
?>