1. process new question DONE
2. add adding time so it will show in history DONE
3. remove all duplicate question DONE
4. add all questionsons from exam DONE
5. add button that refreshes the indexing system DONE

ability to remove question (show warning)  DONE
remove answer (mention in history) DONE
add answer DONE
duplication warning before adding question DONE
last changes page DONE
add footer with credits DONE
item.php add explanation about using ctrl to un/mark correct question DONE
add warning if no correct answer has been marked DONE

Add all original history: DONE
	INSERT INTO history (`actionType`, `entityType`, `userID`, `content`, `qid`, `pid`, `time`)
	SELECT 'A', 'Q', 2, `phraseName`, pID, pID, '2021-01-01 00:00:00' FROM phrases
	WHERE isQuestion = 1

	INSERT INTO history (`actionType`, `entityType`, `userID`, `content`, `qid`, `pid`, `time`)
	SELECT 'A', 'P', 2, `phraseName`, answerOf, pID, '2021-01-01 00:00:00' FROM phrases
	WHERE isQuestion = 0
	
printing page: DONE
	- black&white print
	- color printing
	- table arrangement
	- fix chemical symbols (CO2 subtext becomes: CO_2)
	- option to hide comments
when new question is added log the correct answer in history DONE
add authority check to visibility toggling of a question + answer adding DONE
fixed critical bug in makeJson.php DONE
order question in print view by titles DONE
combine titles of same group DONE