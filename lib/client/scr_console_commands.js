function check_command(string)
{
	var check_string = string.split(" ");
	var command = check_string[0].replace("/", "");
	if (typeof window["command_" + command] == "function")
	{
		eval("command_" + command + "()");
	}
	else
	{
		console.string = "Command '" + command + "' is not defined";
	}
}



function command_test()
{
	console.string = "Test command has been triggered!"
}