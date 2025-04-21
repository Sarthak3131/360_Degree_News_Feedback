// const API_KEY = '3fc4c60978dc4b33acde0a9904194035'; // Replace with your real key
// const PAGE_SIZE = 4;
// let currentPage = 1;
// let allArticles = [];

// const API_URL = `https://newsapi.org/v2/everything?q=india&sortBy=publishedAt&language=en&apiKey=${API_KEY}`;


// const newsContainer = document.getElementById('news-container');
// const loading = document.getElementById('loading');
// const error = document.getElementById('error');
// const prevBtn = document.getElementById('prev-btn');
// const nextBtn = document.getElementById('next-btn');

// function renderPage(page) {
//     newsContainer.innerHTML = "";
//     const start = (page - 1) * PAGE_SIZE;
//     const end = start + PAGE_SIZE;
//     const articles = allArticles.slice(start, end);
  
//     articles.forEach(article => {
//       const card = document.createElement('div');
//       card.className = 'bg-white rounded-2xl shadow-lg overflow-hidden transition hover:scale-105 duration-300 max-w-md mx-auto';
  
//       card.innerHTML = `
//         <img src="${article.urlToImage || 'https://via.placeholder.com/400x200'}" alt="news" class="w-full h-48 object-cover">
//         <div class="p-4">
//           <h3 class="text-xl font-semibold text-gray-800 mb-2">${article.title}</h3>
//           <p class="text-gray-600 text-sm mb-3">${article.description || ''}</p>
//           <a href="${article.url}" target="_blank" class="text-blue-600 hover:underline text-sm font-medium">Read more →</a>
//         </div>
//       `;
//       newsContainer.appendChild(card);
//     });
  
//     prevBtn.disabled = currentPage === 1;
//     nextBtn.disabled = end >= allArticles.length;
//   }
  








//   function renderNews(newsList) {
//     const newsContainer = document.getElementById("news-container");
//     newsContainer.innerHTML = "";
  
//     newsList.forEach(news => {
//       const card = document.createElement("div");
//       card.className = "bg-white shadow-md rounded-lg p-5 flex flex-col h-full";
  
//       card.innerHTML = `
//         <div class="flex flex-col space-y-2 h-full">
//           <h3 class="text-xl font-semibold text-gray-800">${news.title}</h3>
//           <p class="text-gray-600 text-sm">${news.description}</p>
  
//           <div class="flex items-center justify-between mt-auto pt-2">
//             <a href="${news.url}" class="text-blue-600 hover:underline text-sm" target="_blank">Read more →</a>
//             <a href="submit_feedback.php?title=${encodeURIComponent(news.title)}"
//                class="text-sm bg-blue-600 text-white py-1.5 px-3 rounded hover:bg-blue-700">Submit Feedback</a>
//           </div>
//         </div>
//       `;
  
//       newsContainer.appendChild(card);
//     });
  
//     document.getElementById("loading").style.display = "none";
//   }
  





















// function loadNews() {
//   fetch(API_URL)
//     .then(res => res.json())
//     .then(data => {
//       loading.style.display = 'none';
//       if (data.articles && data.articles.length > 0) {
//         allArticles = data.articles;
//         renderPage(currentPage);
//       } else {
//         error.classList.remove('hidden');
//         error.innerText = 'No news found.';
//       }
//     })
//     .catch(err => {
//       loading.style.display = 'none';
//       error.classList.remove('hidden');
//       error.innerText = 'Something went wrong.';
//       console.error(err);
//     });
// }

// prevBtn.addEventListener('click', () => {
//   if (currentPage > 1) {
//     currentPage--;
//     renderPage(currentPage);
//   }
// });

// nextBtn.addEventListener('click', () => {
//   const totalPages = Math.ceil(allArticles.length / PAGE_SIZE);
//   if (currentPage < totalPages) {
//     currentPage++;
//     renderPage(currentPage);
//   }
// });

// loadNews();
