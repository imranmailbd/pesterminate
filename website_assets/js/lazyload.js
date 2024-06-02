// let in_progress_of_getting_newData = false;
// function scrollbarReachedAtBottom(){
// 	alert('test1');
// 	const documentHeight = document.body.scrollHeight;
// 	const scrolled = Math.ceil(window.scrollY+window.innerHeight);
// 	if(scrolled>=documentHeight) return true
// 	else false;
// }
// window.addEventListener("scroll", (event) => {
// 	console.log('test2');
// 	if(scrollbarReachedAtBottom && !in_progress_of_getting_newData){
// 		in_progress_of_getting_newData = true;
// 		//you can a loading effect here.....
// 		const url = 'your_api_here';
// 		const options = {
// 			method:'GET',
// 			headers:{'Content-Type':'application/json'}
// 		}
// 		fetch(url,options).then(response=>response.json()).then(data=>{
// 			in_progress_of_getting_newData = false;
// 			//you need to hide loading effect here if it's displaying.....
// 			//append the newly arrived data into document here
// 		})
// 	}
// });