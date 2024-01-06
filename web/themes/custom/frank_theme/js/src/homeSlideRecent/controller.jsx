const fetchNodes = async () => {
  const response = await fetch('/api/nc?_format=json');
  const data = await response.json();
  return data;
};

export { fetchNodes };
