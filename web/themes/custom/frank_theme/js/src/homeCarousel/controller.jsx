const fetchNodes = async () => {
  const response = await fetch('/api/an?_format=json');
  const data = await response.json();
  return data;
};

export { fetchNodes };
