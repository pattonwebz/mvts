			var $ = jQuery.noConflict();
			$(document).ready(function() {
				var testName = testVariables.testName;
				testName = new Cohorts.Test({
					name: 'MVTS_',
					scope: 1, // Sets the scope for the test and custom variable: 1: Visitor, 2: Session, 3: Page
					cv_slot: 5, // Sets the custom variable slot used in the GoogleAnalyticsAdapter
					sample: 1,
					cohorts: {
						MVTS_default_: {
							onChosen: function() {
								// Nothing is changed here but it's still tracked
							}
						},
						MVTS_variant_: {
							onChosen: function() {
								if (testVariables.testType == 'style') {
									// make style change
								}
								if (testVariables.testType == 'content') {
									// make content change
								}
								//<?php if($options['selectType'] == 'style'){ ?>
									//$('<?php echo $options[target]; ?>').attr( "style", $('<?php echo $options[target]; ?>').attr( "style") + "; <?php echo $options[selectStyle]; ?>: <?php echo $options[styleAtt]; ?>");
								//<?php } ?>
								$(testVariables.testTarget).attr( "style", $(testVariables.testTarget).attr( "style"); + testVariables.selectStyle: testVariables.styleAtt);
								//<?php if($options['selectType'] == 'content'){ ?>
									//$('<?php echo $options[target]; ?>').html( '<?php echo $options[contentChange]; ?>');
								//<?php } ?>
							}
						},
					},
					storageAdapter: {
					nameSpace: 'mvts',
					trackEvent: function(category, action, opt_label, opt_value, int_hit, cv_slot, scope) {
						var len_ga = $('script[src*="analytics.js"]').length;
						var len_gaq = $('script[src*="ga.js"]').length;
						if (testVariables.testTrack == '1'){
							if (len_gaq >= 1) {
								// if using old analytics.js
								_gaq.push(['_trackEvent', category, action, opt_label, opt_value, int_hit]);
							} else if (len_ga >= 1) {
								// if using new ga.js
								// note: using 'ga' should work in most situations however Yoasts GA
								// plugin in Universal mode sets it to __gaTracker to prevent conflits
								// find a way to detect the custom object and set this dynamically
								__gaTracker('send', 'event', category, action, opt_label, opt_value, int_hit);
								//ga('send', 'event', category, action, opt_label, opt_value, int_hit);
							} else {
								console.log ('GA probably not defined or using a different identifier');
							}
						}
					},
					onInitialize: function(inTest, testName, cohort, cv_slot, scope) {
						if(inTest && scope !== 3) {
							this.trackEvent(this.nameSpace, testName, cohort, 0, true, cv_slot, scope);
						}
					},
					onEvent: function(testName, cohort, eventName) {
						this.trackEvent(this.nameSpace, testName, cohort + ' | ' + eventName, 0, false);
					}
				}
				});
				//CLICK FUNCTION -- NEEDS TO BE READDED!!!
				//READDED??
				$(testVariables.testTarget).click(function() {
					testName.trackEvent('Converted'); // Track any events with your storage adapter
				});

			});
