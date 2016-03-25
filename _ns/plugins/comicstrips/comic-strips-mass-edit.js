$( document ).ready(function() {

	// FOR COMIC EDITING:

	editFormVisibility();

	// Insert links to move edit comic boxes up and down
	if ($('.edit-comic').length > 1)
	{
		$('.edit-comic').append('<ul class="movelinks"><li><a href="" class="move-up">Move up</a></li><li><a href="" class="move-down">Move down</a></li></ul>');
		// Remove "move up" from first element and "move down" from last element
		$('.edit-comic').first().find('.move-up').parent().remove();
		$('.edit-comic').last().find('.move-down').parent().remove();

		
		$('.movelinks').on('click', '.move-up', function(event) {
			swapElements($(this), 'prev', 'edit-comic', 'order');
			event.preventDefault();
		});

		$('.movelinks').on('click', '.move-down', function(event) {
			swapElements($(this), 'next', 'edit-comic', 'order');
			event.preventDefault();
		});
	}

	// Change visibility stuff when form is changed
	$('.bulk-schedule-change input[type="radio"][name="schedule"]').on('change', editFormVisibility);

	function editFormVisibility() {
		var selectedSchedule = '';
		var selectedRadio = $('.bulk-schedule-change input[type="radio"][name="schedule"]:checked');
		if (selectedRadio.length > 0)
		{
			selectedSchedule = selectedRadio.val();
		}

		if (selectedSchedule == 'nobulk')
		{
			$('.edit-comic .pubtime-single').slideDown('slow');
		}
		else {
			$('.edit-comic .pubtime-single').slideUp('slow');
		}

		$('.bulk-schedule-change ul ul').hide();
		$('.bulk-schedule-change input[name="schedule"][value="' + selectedSchedule + '"]').closest('li').find('ul').show();
	}


	function swapElements(thisobj, direction, swapclass, orderclass) {
		var thisElement = thisobj.closest('.' + swapclass);
		var thisOrder = thisElement.find('.' + orderclass);
		var thisOrderVal = thisOrder.val();
		var success = false;
		if (direction == 'prev')
		{
			otherElement = thisElement.prev('.' + swapclass);
			if (otherElement.length)
			{
				thisElement.slideUp(function() {
					thisElement.insertBefore(otherElement);
					thisElement.slideDown();
				});

				success = true;
			}
		}
		else {
			otherElement = thisElement.next('.' + swapclass);
			if (otherElement.length)
			{
				thisElement.slideUp(function() {
					thisElement.insertAfter(otherElement);
					thisElement.slideDown();
				});
				success = true;
			}
		}

		if (success)
		{
			// Swap order values
			var otherOrder = otherElement.find('.' + orderclass);
			thisOrder.val(otherOrder.val());
			otherOrder.val(thisOrderVal);

			// Check if move links need to be moved
			if (thisElement.find('.move-down').length == 0)
			{
				thisElement.find('.movelinks').append(otherElement.find('.move-down').parent());
			}
			else if (otherElement.find('.move-down').length == 0)
			{
				otherElement.find('.movelinks').append(thisElement.find('.move-down').parent());
			}

			if (thisElement.find('.move-up').length == 0)
			{
				thisElement.find('.movelinks').prepend(otherElement.find('.move-up').parent());
			}
			else if (otherElement.find('.move-up').length == 0)
			{
				otherElement.find('.movelinks').prepend(thisElement.find('.move-up').parent());
			}

		}
	}


});