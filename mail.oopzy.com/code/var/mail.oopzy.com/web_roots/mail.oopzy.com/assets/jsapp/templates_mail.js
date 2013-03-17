require([
  // Libs
  "use!underscore"
],

function(){
    Conf.JST = Conf.JST || {}; 

	Conf.JST['tl/message'] = _.template(
			  "<div class='message <%=safe_key%>'>"+
			  "<PRE>"+
			  "<a class='saveme' ref='<%= key%>' from='<%= from%>' href='#'>save</a>"+
			  "<a class='deleteme' ref='<%= key%>' elref='<%=safe_key%>' href='#'>delete</a>"+
			  "<b>from:</b> <a href='mailto:<%= from%>'><i><%= from%> </i></a> <%= dt%></br><b>Subject:</b><%= subject%>"+
			  "<span class='more <%=safe_key%>' ref='<%=key%>' elref='<%=safe_key%>'> <%= body%> </a>"+
			  "</PRE>"+
			  "</div>"
			  );

	Conf.JST['tl/save'] = _.template(
			  "<div id='save-confirm' title='Save this email'>"+
			  "<p><span id='wait'></span>"+
			  "<span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>"+
			  "<%=save_1 %> </br>"+
			  "<input id='remailto'> </br><input type='checkbox' id='rememberremail'> <%=save_2 %> </br>"+
			  "<input type='checkbox' id='alwaysremail'> <%=save_3 %> </br><span id='autofrom'></span> <%=save_4 %></p>" +
			  "</div>"
			);

	Conf.JST['tl/verifying'] = _.template(
			"<pre class='hl-yellow'><span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span><%= error%></pre>"
			);

});